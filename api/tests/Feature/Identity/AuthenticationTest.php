<?php

use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;

it('crée un membre anonyme sans aucune donnée identifiante', function () {
    $response = $this->postJson('/api/v1/auth/anonymous', ['device_name' => 'test'])
        ->assertCreated()
        ->assertJsonStructure(['token', 'recovery_code', 'user' => ['id', 'pseudonym', 'role']]);

    $user = User::find($response->json('user.id'));

    expect($user->is_anonymous)->toBeTrue()
        ->and($user->email)->toBeNull()
        ->and($user->phone)->toBeNull()
        ->and($user->hasRole(Role::Member->value))->toBeTrue()
        ->and($response->json('user.role'))->toBe('member');
});

it('retrouve un compte anonyme avec le code de récupération', function () {
    $created = $this->postJson('/api/v1/auth/anonymous')->json();

    $this->postJson('/api/v1/auth/recover', [
        'pseudonym' => $created['user']['pseudonym'],
        'recovery_code' => strtolower($created['recovery_code']),
    ])->assertOk()->assertJsonPath('user.id', $created['user']['id']);

    $this->postJson('/api/v1/auth/recover', [
        'pseudonym' => $created['user']['pseudonym'],
        'recovery_code' => 'AAAA-BBBB-CCCC-DDDD',
    ])->assertUnprocessable();
});

it('connecte par e-mail ou par téléphone', function () {
    userWithRole(Role::Member, ['email' => 'a@example.com', 'phone' => '+22670000000', 'password' => 'secret123']);

    $this->postJson('/api/v1/auth/login', ['login' => 'a@example.com', 'password' => 'secret123'])->assertOk();
    $this->postJson('/api/v1/auth/login', ['login' => '+22670000000', 'password' => 'secret123'])->assertOk();
    $this->postJson('/api/v1/auth/login', ['login' => 'a@example.com', 'password' => 'mauvais'])->assertUnprocessable();
});

it('refuse la connexion d\'un compte suspendu', function () {
    userWithRole(Role::Member, ['email' => 'b@example.com', 'password' => 'secret123'])
        ->forceFill(['suspended_at' => now()])->save();

    $this->postJson('/api/v1/auth/login', ['login' => 'b@example.com', 'password' => 'secret123'])
        ->assertUnprocessable();
});

it('transforme un compte anonyme en compte complet en gardant son historique', function () {
    $member = actingAsRole(Role::Member, ['email' => null, 'password' => null, 'is_anonymous' => true]);
    $member->memberConversations()->create(['status' => 'closed']);

    $this->postJson('/api/v1/me/upgrade', [
        'email' => 'c@example.com',
        'password' => 'motdepasse',
        'password_confirmation' => 'motdepasse',
    ])->assertOk()->assertJsonPath('data.is_anonymous', false);

    expect($member->fresh()->memberConversations()->count())->toBe(1);
});

it('supprime le compte sur demande (droit à l\'effacement)', function () {
    $member = actingAsRole(Role::Member);

    $this->deleteJson('/api/v1/me')->assertNoContent();

    expect(User::find($member->id))->toBeNull();
});
