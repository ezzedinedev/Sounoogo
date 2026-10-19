<?php

namespace App\Domain\Listening\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Listening\Data\VoiceNote;
use App\Domain\Listening\Enums\RiskLevel;
use App\Domain\Listening\Events\MessageSent;
use App\Domain\Listening\Exceptions\ConversationClosed;
use App\Domain\Listening\Jobs\AssessMessageRisk;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\Message;
use App\Domain\Listening\Notifications\ListenerReplied;
use App\Domain\Listening\Services\EmergencyService;
use App\Domain\Listening\Services\RiskDetector;

final class SendMessage
{
    public function __construct(
        private readonly RiskDetector $detector,
        private readonly EmergencyService $emergency,
    ) {}

    /** @return array{0: Message, 1: RiskLevel} */
    public function __invoke(Conversation $conversation, User $sender, string $body, ?VoiceNote $voice = null): array
    {
        if (! $conversation->isOpen()) {
            throw new ConversationClosed;
        }

        $fromMember = $sender->id === $conversation->member_id;
        // Un message vocal n'est pas analysé automatiquement (pas de transcription) : l'écoutant l'entend.
        $risk = $fromMember && $voice === null ? $this->detector->assess($body) : RiskLevel::None;

        $message = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'kind' => $voice ? 'audio' : 'text',
            'body' => $voice?->label() ?? $body,
            'audio_path' => $voice?->path,
            'audio_mime' => $voice?->mime,
            'audio_duration' => $voice?->duration,
            'is_flagged' => $risk !== RiskLevel::None,
        ]);

        // Délai de première réponse, suivi par la supervision.
        if (! $fromMember && $conversation->first_response_at === null) {
            $conversation->first_response_at = now();
        }
        $conversation->touch();
        MessageSent::dispatch($message);

        // Les mots-clés réagissent tout de suite ; l'IA, si elle est activée, relit ensuite le message dans son contexte.
        if ($risk !== RiskLevel::None) {
            $this->emergency->raise($sender, 'keyword', $risk, $conversation, $message);
        }
        if ($fromMember && $voice === null && $risk !== RiskLevel::High && config('sunoogo.ai.enabled')) {
            AssessMessageRisk::dispatch($message);
        }

        // Le membre est prévenu sur son téléphone quand son écoutant lui répond.
        if (! $fromMember) {
            $conversation->member->notify(new ListenerReplied($conversation));
        }

        return [$message, $risk];
    }
}
