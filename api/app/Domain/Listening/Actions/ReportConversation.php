<?php

namespace App\Domain\Listening\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Listening\Enums\ReportStatus;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\Message;
use App\Domain\Listening\Models\Report;

final class ReportConversation
{
    public function __invoke(User $reporter, Conversation $conversation, string $reason, ?string $details = null, ?Message $message = null): Report
    {
        $reported = $reporter->id === $conversation->member_id
            ? $conversation->listener_id
            : $conversation->member_id;

        return Report::create([
            'reporter_id' => $reporter->id,
            'reported_user_id' => $reported,
            'conversation_id' => $conversation->id,
            'message_id' => $message?->id,
            'reason' => $reason,
            'details' => $details,
            'status' => ReportStatus::Pending,
        ]);
    }
}
