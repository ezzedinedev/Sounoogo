<?php

namespace App\Infrastructure\AI;

use App\Domain\Listening\Contracts\ListenerCopilot;
use App\Domain\Listening\Data\ConversationSummary;
use App\Domain\Listening\Exceptions\CopilotUnavailable;
use App\Domain\Listening\Models\Conversation;

final class DisabledListenerCopilot implements ListenerCopilot
{
    public function suggestReplies(Conversation $conversation): array
    {
        throw CopilotUnavailable::disabled();
    }

    public function summarize(Conversation $conversation): ConversationSummary
    {
        throw CopilotUnavailable::disabled();
    }
}
