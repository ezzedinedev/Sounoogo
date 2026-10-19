<?php

namespace App\Domain\Listening\Jobs;

use App\Domain\Listening\Contracts\RiskClassifier;
use App\Domain\Listening\Models\Message;
use App\Domain\Listening\Services\EmergencyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Second regard de l'IA sur un message de membre, après les mots-clés. Si elle perçoit un risque plus élevé
 * que celui déjà connu, un superviseur est alerté avec son explication. Rien n'est envoyé au membre :
 * c'est l'humain qui confirme et agit.
 */
final class AssessMessageRisk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(public Message $message)
    {
        $this->afterCommit();
    }

    public function handle(RiskClassifier $classifier, EmergencyService $emergency): void
    {
        $conversation = $this->message->conversation;
        $assessment = $classifier->assess($conversation, $this->message);

        if ($assessment === null || $assessment->level->weight() <= $conversation->risk_level->weight()) {
            return;
        }

        $this->message->update(['is_flagged' => true]);
        $emergency->raise($conversation->member, 'ai', $assessment->level, $conversation, $this->message, $assessment->reason);
    }
}
