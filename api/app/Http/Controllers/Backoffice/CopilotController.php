<?php

namespace App\Http\Controllers\Backoffice;

use App\Domain\Listening\Contracts\ListenerCopilot;
use App\Domain\Listening\Exceptions\CopilotUnavailable;
use App\Domain\Listening\Models\Conversation;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Copilote de l'écoutant : propositions de formulation et résumé, à la demande.
 * Rien n'est envoyé au membre sans que l'écoutant l'ait relu et validé.
 */
class CopilotController extends Controller
{
    public function suggestions(Conversation $conversation, ListenerCopilot $copilot): JsonResponse
    {
        $this->authorize('participate', $conversation);

        try {
            return response()->json(['suggestions' => $copilot->suggestReplies($conversation)]);
        } catch (CopilotUnavailable $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }

    public function summary(Conversation $conversation, ListenerCopilot $copilot): JsonResponse
    {
        $this->authorize('view', $conversation);

        try {
            $summary = $copilot->summarize($conversation);

            return response()->json(['summary' => $summary->summary, 'watch_points' => $summary->watchPoints]);
        } catch (CopilotUnavailable $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }
}
