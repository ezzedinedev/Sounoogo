<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Billing\Actions\RefreshPaymentStatus;
use App\Domain\Billing\Actions\SettlePayment;
use App\Domain\Billing\Enums\PaymentProvider;
use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Models\Payment;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Infrastructure\Payments\PaymentGatewayManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

/**
 * @tags Paiements
 */
class PaymentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PaymentResource::collection(
            Payment::where('user_id', $request->user()->id)->latest()->paginate(20)
        );
    }

    /** Suivre l'état d'un paiement après le retour du prestataire (l'état est revérifié s'il est en attente). */
    public function show(Request $request, Payment $payment, RefreshPaymentStatus $refresh): PaymentResource
    {
        abort_unless($payment->user_id === $request->user()->id, 404);

        return new PaymentResource($refresh($payment));
    }

    /**
     * Notifications des prestataires de paiement.
     *
     * @unauthenticated
     */
    public function webhook(Request $request, string $provider, PaymentGatewayManager $gateways, SettlePayment $settle): JsonResponse
    {
        $provider = PaymentProvider::tryFrom($provider) ?? abort(404);
        $outcome = $gateways->for($provider)->parseWebhook($request);

        if ($outcome === null) {
            Log::warning('Notification de paiement rejetée', ['provider' => $provider->value, 'ip' => $request->ip()]);

            return response()->json(['message' => 'Notification invalide.'], 400);
        }

        $payment = Payment::query()
            ->where('provider', $provider)
            ->where(fn ($q) => $q
                ->when($outcome->reference, fn ($q) => $q->orWhere('reference', $outcome->reference))
                ->when($outcome->providerReference, fn ($q) => $q->orWhere('provider_reference', $outcome->providerReference)))
            ->first();

        if ($payment === null) {
            return response()->json(['message' => 'Paiement inconnu.'], 404);
        }

        $settle($payment, $outcome->status, $outcome->payload);

        return response()->json(['message' => 'OK']);
    }

    /**
     * Page de paiement simulé (hors production) : `?status=succeeded|failed`.
     *
     * @unauthenticated
     */
    public function mock(Request $request, string $reference, SettlePayment $settle): JsonResponse
    {
        abort_if(app()->isProduction(), 404);

        $payment = Payment::where('reference', $reference)->where('provider', PaymentProvider::Mock)->firstOrFail();
        $status = PaymentStatus::tryFrom((string) $request->query('status', 'succeeded')) ?? PaymentStatus::Succeeded;

        return response()->json(['data' => new PaymentResource($settle($payment, $status))]);
    }
}
