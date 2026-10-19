<?php

use App\Domain\Identity\Enums\Role;
use App\Domain\Listening\Models\Conversation;

it('montre la file, les écoutes et les alertes au superviseur', function () {
    $member = userWithRole(Role::Member);
    Conversation::create(['member_id' => $member->id, 'topic' => 'grief', 'risk_level' => 'high']);

    $this->actingAs(userWithRole(Role::Supervisor))
        ->get(route('backoffice.supervision.index'))
        ->assertOk()
        ->assertSee('Supervision')
        ->assertSee($member->pseudonym)
        ->assertSee('Deuil');
});

it('fournit un fragment rafraîchi en direct', function () {
    $this->actingAs(userWithRole(Role::Supervisor))
        ->get(route('backoffice.supervision.live'))
        ->assertOk()
        ->assertSee('File d\'attente')
        ->assertDontSee('<html', false);
});

it('est réservé aux superviseurs', function () {
    $this->actingAs(userWithRole(Role::Listener))
        ->get(route('backoffice.supervision.index'))
        ->assertForbidden();
});
