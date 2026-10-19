<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Listening\Models\Conversation;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

/**
 * @tags Écoute anonyme
 */
class PreferredListenerController extends Controller
{
    /**
     * Reparler à la même personne.
     *
     * Le membre désigne l'écoutant d'une de ses conversations passées : ses prochaines demandes
     * lui sont confiées en priorité s'il est disponible.
     */
    public function store(Request $request): UserResource
    {
        $data = $request->validate(['conversation_id' => ['required', 'integer']]);
        $conversation = Conversation::query()
            ->where('member_id', $request->user()->id)
            ->whereNotNull('listener_id')
            ->findOrFail($data['conversation_id']);

        $request->user()->update(['preferred_listener_id' => $conversation->listener_id]);

        return new UserResource($request->user()->load('preferredListener'));
    }

    public function destroy(Request $request): UserResource
    {
        $request->user()->update(['preferred_listener_id' => null]);

        return new UserResource($request->user());
    }
}
