<?php

namespace App\Infrastructure\Payments;

use App\Domain\Billing\Contracts\PaymentGateway;
use App\Domain\Billing\Data\CheckoutSession;
use App\Domain\Billing\Data\WebhookOutcome;
use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Wave Checkout API — https://docs.wave.com/checkout
 */
final class WaveGateway implements PaymentGateway
{
    public function createCheckout(Payment $payment): CheckoutSession
    {
        $config = config('sunoogo.payments');

        $response = Http::withToken($config['wave']['api_key'])
            ->baseUrl($config['wave']['base_url'])
            ->acceptJson()
            ->timeout(15)
            ->post('/v1/checkout/sessions', [
                'amount' => (string) $payment->amount,
                'currency' => $payment->currency,
                'client_reference' => $payment->reference,
                'success_url' => $config['success_url'].'?ref='.$payment->reference,
                'error_url' => $config['error_url'].'?ref='.$payment->reference,
            ])
            ->throw();

        return new CheckoutSession(
            checkoutUrl: $response->json('wave_launch_url'),
            providerReference: $response->json('id'),
        );
    }

    public function parseWebhook(Request $request): ?WebhookOutcome
    {
        if (! $this->hasValidSignature($request)) {
            return null;
        }

        $data = $request->input('data', []);

        $status = match ($request->input('type')) {
            'checkout.session.completed' => ($data['payment_status'] ?? null) === 'succeeded'
                ? PaymentStatus::Succeeded
                : PaymentStatus::Failed,
            'checkout.session.payment_failed' => PaymentStatus::Failed,
            default => null,
        };

        if ($status === null) {
            return null;
        }

        return new WebhookOutcome(
            status: $status,
            reference: $data['client_reference'] ?? null,
            providerReference: $data['id'] ?? null,
            payload: $request->all(),
        );
    }

    /** En-tête "Wave-Signature: t=<timestamp>,v1=<hmac>" — HMAC-SHA256 de timestamp + corps brut. */
    private function hasValidSignature(Request $request): bool
    {
        $secret = config('sunoogo.payments.wave.webhook_secret');
        $header = (string) $request->header('Wave-Signature');

        if (blank($secret) || $header === '') {
            return false;
        }

        $parts = collect(explode(',', $header))
            ->mapWithKeys(function (string $part) {
                [$key, $value] = array_pad(explode('=', $part, 2), 2, '');

                return [trim($key) => trim($value)];
            });

        $expected = hash_hmac('sha256', $parts->get('t', '').$request->getContent(), $secret);

        return hash_equals($expected, (string) $parts->get('v1'));
    }
}
