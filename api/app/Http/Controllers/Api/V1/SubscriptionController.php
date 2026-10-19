<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Billing\Actions\InitiatePayment;
use App\Domain\Billing\Actions\StartSubscription;
use App\Domain\Billing\Models\Plan;
use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\SubscribeRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\SubscriptionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Abonnements
 */
class SubscriptionController extends Controller
{
    public function current(Request $request): JsonResponse
    {
        $subscription = $request->user()->activeSubscription();

        return response()->json([
            'data' => $subscription ? new SubscriptionResource($subscription->load('plan')) : null,
        ]);
    }

    /** Souscrire (ou renouveler) : l'abonnement est activé une fois le paiement reçu. */
    public function store(SubscribeRequest $request, StartSubscription $start, InitiatePayment $initiate): PaymentResource
    {
        $plan = Plan::where('code', $request->validated('plan'))->firstOrFail();
        $subscription = $start($request->user(), $plan);

        return new PaymentResource($initiate($request->user(), $subscription, $plan->price, $request->provider(), $request->phone()));
    }
}
