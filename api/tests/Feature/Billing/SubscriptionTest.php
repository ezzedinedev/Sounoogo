<?php

use App\Domain\Identity\Enums\Role;

it('active l\'abonnement après paiement', function () {
    actingAsRole(Role::Member);

    $reference = $this->postJson('/api/v1/subscription', ['plan' => 'essentiel', 'provider' => 'mock'])
        ->assertCreated()
        ->assertJsonPath('data.amount', 2500)
        ->json('data.reference');

    $this->getJson('/api/v1/subscription')->assertJsonPath('data', null);

    $this->getJson("/api/v1/payments/mock/{$reference}?status=succeeded");

    $this->getJson('/api/v1/subscription')
        ->assertJsonPath('data.status', 'active')
        ->assertJsonPath('data.plan.code', 'essentiel');
});

it('n\'active rien si le paiement échoue', function () {
    actingAsRole(Role::Member);
    $reference = $this->postJson('/api/v1/subscription', ['plan' => 'essentiel', 'provider' => 'mock'])->json('data.reference');

    $this->getJson("/api/v1/payments/mock/{$reference}?status=failed");

    $this->getJson('/api/v1/subscription')->assertJsonPath('data', null);
});

it('ignore une notification de paiement rejouée', function () {
    actingAsRole(Role::Member);
    $reference = $this->postJson('/api/v1/subscription', ['plan' => 'essentiel', 'provider' => 'mock'])->json('data.reference');

    $this->getJson("/api/v1/payments/mock/{$reference}?status=succeeded");
    $this->getJson("/api/v1/payments/mock/{$reference}?status=failed")->assertJsonPath('data.status', 'succeeded');
});

it('refuse un moyen de paiement non configuré', function () {
    actingAsRole(Role::Member);

    $this->postJson('/api/v1/subscription', ['plan' => 'essentiel', 'provider' => 'wave'])
        ->assertStatus(503);
});

it('rejette une notification Wave à la signature invalide', function () {
    config(['sunoogo.payments.wave.api_key' => 'key', 'sunoogo.payments.wave.webhook_secret' => 'secret']);

    $this->postJson('/api/v1/payments/webhooks/wave', ['type' => 'checkout.session.completed'], ['Wave-Signature' => 't=1,v1=bad'])
        ->assertStatus(400);
});

it('accepte une notification Wave correctement signée', function () {
    config(['sunoogo.payments.wave.api_key' => 'key', 'sunoogo.payments.wave.webhook_secret' => 'secret']);
    Http::fake(['*/v1/checkout/sessions' => Http::response(['id' => 'cos_123', 'wave_launch_url' => 'https://pay.wave.com/c/cos_123'])]);
    actingAsRole(Role::Member);

    $reference = $this->postJson('/api/v1/subscription', ['plan' => 'essentiel', 'provider' => 'wave'])
        ->assertJsonPath('data.checkout_url', 'https://pay.wave.com/c/cos_123')
        ->json('data.reference');

    $body = json_encode(['type' => 'checkout.session.completed', 'data' => ['id' => 'cos_123', 'client_reference' => $reference, 'payment_status' => 'succeeded']]);
    $signature = 't=1700000000,v1='.hash_hmac('sha256', '1700000000'.$body, 'secret');

    $this->call('POST', '/api/v1/payments/webhooks/wave', server: ['HTTP_WAVE_SIGNATURE' => $signature, 'CONTENT_TYPE' => 'application/json'], content: $body)
        ->assertOk();

    $this->getJson('/api/v1/subscription')->assertJsonPath('data.status', 'active');
});
