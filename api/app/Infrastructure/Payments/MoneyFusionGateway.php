<?php

namespace App\Infrastructure\Payments;

use App\Domain\Billing\Contracts\ChecksPaymentStatus;
use App\Domain\Billing\Contracts\PaymentGateway;
use App\Domain\Billing\Data\CheckoutSession;
use App\Domain\Billing\Data\Payer;
use App\Domain\Billing\Data\WebhookOutcome;
use App\Domain\Billing\Enums\PaymentProvider;
use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Models\Payment;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * MoneyFusion (FusionPay) — https://docs.moneyfusion.net/fr/webapi
 *
 * Les notifications ne sont pas signées : leur contenu n'est jamais cru sur parole.
 * On n'en retient que le jeton, puis on interroge MoneyFusion pour connaître l'état réel.
 * L'adresse IP du serveur doit être déclarée dans le tableau de bord MoneyFusion.
 */
final class MoneyFusionGateway implements ChecksPaymentStatus, PaymentGateway
{
    /** Données personnelles retirées avant d'enregistrer une réponse du prestataire. */
    private const PERSONAL_FIELDS = ['numeroSend', 'nomclient'];

    public function createCheckout(Payment $payment, Payer $payer): CheckoutSession
    {
        if (blank($payer->phone)) {
            throw new RuntimeException('MoneyFusion exige le numéro Mobile Money du payeur.');
        }

        $response = Http::acceptJson()
            ->timeout(20)
            ->post(config('sunoogo.payments.moneyfusion.api_url'), [
                'totalPrice' => $payment->amount,
                'article' => [[$payment->meta['label'] ?? 'SUNOOGO' => $payment->amount]],
                'personal_Info' => [['reference' => $payment->reference]],
                'numeroSend' => $payer->phone,
                'nomclient' => $payer->name,
                'return_url' => config('sunoogo.payments.success_url').'?ref='.$payment->reference,
                'webhook_url' => route('api.v1.payments.webhook', PaymentProvider::MoneyFusion->value),
            ])
            ->throw();

        if ($response->json('statut') !== true || blank($response->json('token')) || blank($response->json('url'))) {
            throw new RuntimeException('MoneyFusion a refusé la création du paiement : '.$response->json('message', 'réponse inattendue'));
        }

        return new CheckoutSession(checkoutUrl: $response->json('url'), providerReference: $response->json('token'));
    }

    public function parseWebhook(Request $request): ?WebhookOutcome
    {
        $token = $request->input('tokenPay');

        return is_string($token) && $token !== '' ? $this->verify($token) : null;
    }

    public function fetchStatus(Payment $payment): ?WebhookOutcome
    {
        return $payment->provider_reference ? $this->verify($payment->provider_reference) : null;
    }

    /** Source de vérité : l'état du paiement tel que MoneyFusion le renvoie pour ce jeton. */
    private function verify(string $token): ?WebhookOutcome
    {
        try {
            $response = Http::acceptJson()
                ->timeout(15)
                ->get(rtrim(config('sunoogo.payments.moneyfusion.status_url'), '/').'/'.rawurlencode($token))
                ->throw();
        } catch (RequestException) {
            return null;
        }

        $data = $response->json('data');

        if ($response->json('statut') !== true || ! is_array($data) || ($data['tokenPay'] ?? null) !== $token) {
            return null;
        }

        $status = match ($data['statut'] ?? null) {
            'paid' => PaymentStatus::Succeeded,
            'failure', 'failed', 'no paid' => PaymentStatus::Failed,
            'pending' => PaymentStatus::Pending,
            default => null,
        };

        if ($status === null) {
            return null;
        }

        return new WebhookOutcome(
            status: $status,
            reference: collect($data['personal_Info'] ?? [])->pluck('reference')->filter()->first(),
            providerReference: $token,
            payload: Arr::except($data, self::PERSONAL_FIELDS),
        );
    }
}
