<?php

namespace App\Infrastructure\Payments;

use App\Domain\Billing\Contracts\PaymentGateway;
use App\Domain\Billing\Data\CheckoutSession;
use App\Domain\Billing\Data\WebhookOutcome;
use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Orange Money Web Payment (API Orange Developer).
 * À valider avec la documentation et le contrat marchand Orange Burkina Faso
 * (chemin de l'API et pays) avant la mise en production.
 */
final class OrangeMoneyGateway implements PaymentGateway
{
    public function createCheckout(Payment $payment): CheckoutSession
    {
        $config = config('sunoogo.payments');

        $response = Http::withToken($this->accessToken())
            ->baseUrl($config['orange_money']['base_url'])
            ->acceptJson()
            ->timeout(15)
            ->post($config['orange_money']['webpay_path'], [
                'merchant_key' => $config['orange_money']['merchant_key'],
                'currency' => $config['orange_money']['currency'],
                'order_id' => $payment->reference,
                'amount' => $payment->amount,
                'return_url' => $config['success_url'].'?ref='.$payment->reference,
                'cancel_url' => $config['error_url'].'?ref='.$payment->reference,
                'notif_url' => route('api.v1.payments.webhook', ['provider' => 'orange_money']),
                'lang' => 'fr',
                'reference' => 'SUNOOGO',
            ])
            ->throw();

        $payment->update(['meta' => [...($payment->meta ?? []), 'notif_token' => $response->json('notif_token')]]);

        return new CheckoutSession(
            checkoutUrl: $response->json('payment_url'),
            providerReference: $response->json('pay_token'),
        );
    }

    public function parseWebhook(Request $request): ?WebhookOutcome
    {
        $reference = $request->input('order_id');
        $payment = $reference ? Payment::firstWhere('reference', $reference) : null;

        // Orange renvoie le notif_token reçu à la création : c'est notre preuve d'authenticité.
        if ($payment === null || ! hash_equals((string) ($payment->meta['notif_token'] ?? ''), (string) $request->input('notif_token'))) {
            return null;
        }

        $status = match ($request->input('status')) {
            'SUCCESS' => PaymentStatus::Succeeded,
            'FAILED', 'EXPIRED' => PaymentStatus::Failed,
            default => null,
        };

        return $status ? new WebhookOutcome(
            status: $status,
            reference: $reference,
            providerReference: $request->input('txnid'),
            payload: $request->all(),
        ) : null;
    }

    private function accessToken(): string
    {
        return Cache::remember('orange_money.token', now()->addMinutes(50), function () {
            $config = config('sunoogo.payments.orange_money');

            return Http::asForm()
                ->withBasicAuth($config['client_id'], $config['client_secret'])
                ->baseUrl($config['base_url'])
                ->post('/oauth/v3/token', ['grant_type' => 'client_credentials'])
                ->throw()
                ->json('access_token');
        });
    }
}
