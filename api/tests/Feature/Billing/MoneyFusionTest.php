<?php

use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Billing\Models\Payment;
use App\Domain\Identity\Enums\Role;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

const MF_API = 'https://www.pay.moneyfusion.net/SUNOOGO/abc123/pay/';
const MF_STATUS = 'https://pay.moneyfusion.net/paiementNotif/*';

beforeEach(function () {
    config(['sunoogo.payments.moneyfusion.api_url' => MF_API]);
});

/** Réponse de la vérification d'état MoneyFusion pour un jeton. */
function moneyFusionStatus(string $token, string $status, ?string $reference): array
{
    return [
        'statut' => true,
        'message' => 'details paiement',
        'data' => [
            'tokenPay' => $token,
            'numeroSend' => '70123456',
            'nomclient' => 'Baobab-204',
            'personal_Info' => [['reference' => $reference]],
            'numeroTransaction' => 'TX-1',
            'Montant' => 2500,
            'frais' => 50,
            'statut' => $status,
            'moyen' => 'orange',
        ],
    ];
}

function subscribeWithMoneyFusion(): Payment
{
    test()->postJson('/api/v1/subscription', ['plan' => 'essentiel', 'provider' => 'moneyfusion', 'phone' => '70 12 34 56'])
        ->assertCreated()
        ->assertJsonPath('data.checkout_url', 'https://payin.moneyfusion.net/payment/tok_1/Marchand');

    return Payment::latest('id')->firstOrFail();
}

it('crée le paiement chez MoneyFusion avec le pseudonyme, jamais le vrai nom', function () {
    $member = actingAsRole(Role::Member);
    Http::fake([MF_API => Http::response(['statut' => true, 'token' => 'tok_1', 'message' => 'paiement en cours', 'url' => 'https://payin.moneyfusion.net/payment/tok_1/Marchand'])]);

    $payment = subscribeWithMoneyFusion();

    expect($payment->provider_reference)->toBe('tok_1');
    Http::assertSent(fn (Request $request) => $request->url() === MF_API
        && $request['totalPrice'] === 2500
        && $request['numeroSend'] === '70123456'
        && $request['nomclient'] === $member->pseudonym
        && $request['personal_Info'][0]['reference'] === $payment->reference
        && str_ends_with($request['webhook_url'], '/api/v1/payments/webhooks/moneyfusion'));
});

it('exige le numéro Mobile Money', function () {
    actingAsRole(Role::Member);

    $this->postJson('/api/v1/subscription', ['plan' => 'essentiel', 'provider' => 'moneyfusion'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('phone');
});

it('active l\'abonnement quand MoneyFusion confirme le paiement', function () {
    actingAsRole(Role::Member);
    Http::fake([MF_API => Http::response(['statut' => true, 'token' => 'tok_1', 'url' => 'https://payin.moneyfusion.net/payment/tok_1/Marchand'])]);
    $payment = subscribeWithMoneyFusion();
    Http::fake([MF_STATUS => Http::response(moneyFusionStatus('tok_1', 'paid', $payment->reference))]);

    $this->postJson('/api/v1/payments/webhooks/moneyfusion', ['event' => 'payin.session.completed', 'tokenPay' => 'tok_1'])->assertOk();

    expect($payment->fresh()->status)->toBe(PaymentStatus::Succeeded)
        ->and($payment->fresh()->meta['last_notification'])->not->toHaveKeys(['numeroSend', 'nomclient']);
    $this->getJson('/api/v1/subscription')->assertJsonPath('data.status', 'active');
});

it('ne croit pas une notification forgée : l\'état vient de MoneyFusion', function () {
    actingAsRole(Role::Member);
    Http::fake([MF_API => Http::response(['statut' => true, 'token' => 'tok_1', 'url' => 'https://payin.moneyfusion.net/payment/tok_1/Marchand'])]);
    $payment = subscribeWithMoneyFusion();
    Http::fake([MF_STATUS => Http::response(moneyFusionStatus('tok_1', 'pending', $payment->reference))]);

    $this->postJson('/api/v1/payments/webhooks/moneyfusion', ['event' => 'payin.session.completed', 'tokenPay' => 'tok_1', 'statut' => 'paid'])->assertOk();

    expect($payment->fresh()->status)->toBe(PaymentStatus::Pending);
});

it('rejette une notification dont le jeton est inconnu de MoneyFusion', function () {
    Http::fake([MF_STATUS => Http::response(['statut' => false, 'message' => 'introuvable'], 404)]);

    $this->postJson('/api/v1/payments/webhooks/moneyfusion', ['tokenPay' => 'faux'])->assertStatus(400);
});

it('récupère le paiement au suivi même si la notification s\'est perdue', function () {
    actingAsRole(Role::Member);
    Http::fake([MF_API => Http::response(['statut' => true, 'token' => 'tok_1', 'url' => 'https://payin.moneyfusion.net/payment/tok_1/Marchand'])]);
    $payment = subscribeWithMoneyFusion();
    Http::fake([MF_STATUS => Http::response(moneyFusionStatus('tok_1', 'no paid', $payment->reference))]);

    $this->getJson("/api/v1/payments/{$payment->reference}")->assertJsonPath('data.status', 'failed');
});
