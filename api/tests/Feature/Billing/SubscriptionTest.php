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

    $this->postJson('/api/v1/subscription', ['plan' => 'essentiel', 'provider' => 'moneyfusion', 'phone' => '70123456'])
        ->assertStatus(503);
});
