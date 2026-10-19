<?php

namespace App\Domain\Listening\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Listening\Enums\RiskLevel;
use App\Domain\Listening\Events\MessageSent;
use App\Domain\Listening\Exceptions\ConversationClosed;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\Message;
use App\Domain\Listening\Services\EmergencyService;
use App\Domain\Listening\Services\RiskDetector;

final class SendMessage
{
    public function __construct(
        private readonly RiskDetector $detector,
        private readonly EmergencyService $emergency,
    ) {}

    /** @return array{0: Message, 1: RiskLevel} */
    public function __invoke(Conversation $conversation, User $sender, string $body): array
    {
        if (! $conversation->isOpen()) {
            throw new ConversationClosed;
        }

        $risk = $sender->id === $conversation->member_id
            ? $this->detector->assess($body)
            : RiskLevel::None;

        $message = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'kind' => 'text',
            'body' => $body,
            'is_flagged' => $risk !== RiskLevel::None,
        ]);

        $conversation->touch();
        MessageSent::dispatch($message);

        if ($risk !== RiskLevel::None) {
            $this->emergency->raise($sender, 'keyword', $risk, $conversation, $message);
        }

        return [$message, $risk];
    }
}
