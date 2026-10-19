<?php

namespace App\Infrastructure\Payments;

use App\Domain\Billing\Contracts\PaymentGateway;
use App\Domain\Billing\Data\CheckoutSession;
use App\Domain\Billing\Data\Payer;
use App\Domain\Billing\Data\WebhookOutcome;
use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Models\Payment;
use Illuminate\Http\Request;

/** Paiement simulé pour le développement et les tests. Jamais disponible en production. */
final class MockGateway implements PaymentGateway
{
    public function createCheckout(Payment $payment, Payer $payer): CheckoutSession
    {
        return new CheckoutSession(
            checkoutUrl: url("/api/v1/payments/mock/{$payment->reference}"),
            providerReference: 'mock_'.$payment->reference,
        );
    }

    public function parseWebhook(Request $request): ?WebhookOutcome
    {
        $status = PaymentStatus::tryFrom((string) $request->input('status'));

        if ($status === null) {
            return null;
        }

        return new WebhookOutcome(status: $status, reference: $request->input('reference'), payload: $request->all());
    }
}
