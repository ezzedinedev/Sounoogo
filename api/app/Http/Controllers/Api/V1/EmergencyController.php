<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Listening\Enums\RiskLevel;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Services\EmergencyService;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmergencyContactResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Urgence
 */
class EmergencyController extends Controller
{
    /**
     * Bouton SOS.
     *
     * Alerte immédiatement un superviseur et renvoie les numéros d'urgence locaux.
     */
    public function __invoke(Request $request, EmergencyService $emergency): JsonResponse
    {
        $user = $request->user();

        $conversation = $user->memberConversations()
            ->where('status', '!=', 'closed')
            ->latest()
            ->first();

        $emergency->raise($user, 'sos', RiskLevel::High, $conversation instanceof Conversation ? $conversation : null);

        return response()->json([
            'message' => 'Tu n\'es pas seul·e. Un superviseur a été prévenu. Si tu es en danger immédiat, appelle l\'un de ces numéros.',
            'emergency_contacts' => EmergencyContactResource::collection($emergency->contactsFor($user->country)),
        ], 201);
    }
}
