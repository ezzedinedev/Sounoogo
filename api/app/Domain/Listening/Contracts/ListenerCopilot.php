<?php

namespace App\Domain\Listening\Contracts;

use App\Domain\Listening\Data\ConversationSummary;
use App\Domain\Listening\Exceptions\CopilotUnavailable;
use App\Domain\Listening\Models\Conversation;

/**
 * Aide à l'écoutant : propositions de formulation et résumé. L'écoutant reste seul à écrire au membre.
 */
interface ListenerCopilot
{
    /**
     * @return list<string>
     *
     * @throws CopilotUnavailable
     */
    public function suggestReplies(Conversation $conversation): array;

    /** @throws CopilotUnavailable */
    public function summarize(Conversation $conversation): ConversationSummary;
}
