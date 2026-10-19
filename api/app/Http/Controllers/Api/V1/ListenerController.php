<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Listening\Enums\ConversationStatus;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Services\ListenerQueue;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @tags Écoutants
 */
class ListenerController extends Controller
{
    /** Se déclarer disponible ou indisponible pour recevoir des conversations. */
    public function availability(Request $request, ListenerQueue $queue): JsonResponse
    {
        $data = $request->validate(['is_available' => ['required', 'boolean']]);
        $request->user()->update($data);

        // Dès qu'un écoutant se rend disponible, il prend la conversation la plus prioritaire.
        $assigned = $data['is_available'] ? $queue->takeNext($request->user()) : null;

        return response()->json([
            'is_available' => $request->user()->is_available,
            'assigned_conversation' => $assigned ? new ConversationResource($assigned->load('member')) : null,
        ]);
    }

    /** Conversations en attente d'un écoutant, les plus à risque d'abord. */
    public function queue(): AnonymousResourceCollection
    {
        return ConversationResource::collection(
            Conversation::query()
                ->where('status', ConversationStatus::Waiting)
                ->with('member')
                ->orderByRaw("case risk_level when 'high' then 0 when 'medium' then 1 else 2 end")
                ->oldest()
                ->get()
        );
    }

    public function take(Request $request, Conversation $conversation, ListenerQueue $queue): ConversationResource
    {
        $this->authorize('take', $conversation);

        $conversation = $queue->assign($conversation, $request->user());
        abort_unless($conversation->listener_id === $request->user()->id, 409, 'Cette conversation a déjà été prise.');

        return new ConversationResource($conversation->load(['member', 'listener']));
    }
}
