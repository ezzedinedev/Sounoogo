<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Billing\Models\Plan;
use App\Domain\Listening\Services\EmergencyService;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmergencyContactResource;
use App\Http\Resources\PlanResource;
use App\Infrastructure\Payments\PaymentGatewayManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Référentiel
 */
class MetaController extends Controller
{
    /**
     * Données de démarrage de l'application : numéros d'urgence, moyens de paiement, offres.
     *
     * @unauthenticated
     */
    public function __invoke(Request $request, EmergencyService $emergency, PaymentGatewayManager $gateways): JsonResponse
    {
        $country = $request->query('country', $request->user('sanctum')?->country);

        return response()->json([
            'emergency_contacts' => EmergencyContactResource::collection($emergency->contactsFor($country)),
            'payment_providers' => collect($gateways->available())
                ->map(fn ($provider) => ['value' => $provider->value, 'label' => $provider->label(), 'requires_phone' => $provider->requiresPhone()]),
            'plans' => PlanResource::collection(Plan::where('is_active', true)->orderBy('price')->get()),
        ]);
    }
}
