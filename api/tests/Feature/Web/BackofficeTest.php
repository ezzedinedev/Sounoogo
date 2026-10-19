<?php

use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\Report;
use App\Domain\Listening\Services\ListenerQueue;
use Database\Seeders\DemoSeeder;

beforeEach(fn () => $this->seed(DemoSeeder::class));

it('connecte un membre de l\'équipe', function () {
    $this->post('/admin/connexion', ['email' => 'listener@sunoogo.local', 'password' => 'password'])
        ->assertRedirect('/admin');

    $this->assertAuthenticated();
});

it('refuse l\'espace équipe aux membres', function () {
    $this->post('/admin/connexion', ['email' => 'member@sunoogo.local', 'password' => 'password'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('redirige les invités vers la connexion', function () {
    $this->get('/admin')->assertRedirect('/admin/connexion');
});

it('ouvre toutes les pages d\'administration', function (string $uri) {
    $this->actingAs(userWithRole(Role::Admin))->get($uri)->assertOk();
})->with([
    '/admin', '/admin/ecoute', '/admin/alertes', '/admin/signalements', '/admin/utilisateurs',
    '/admin/utilisateurs/create', '/admin/professionnels', '/admin/professionnels/create',
    '/admin/reservations', '/admin/paiements', '/admin/formations', '/admin/formations/create',
    '/admin/offres', '/admin/numeros-urgence', '/admin/journal',
]);

it('limite l\'écoutant à son périmètre', function () {
    $listener = userWithRole(Role::Listener);

    $this->actingAs($listener)->get('/admin/ecoute')->assertOk();
    $this->actingAs($listener)->get('/admin/utilisateurs')->assertForbidden();
    $this->actingAs($listener)->get('/admin/alertes')->assertForbidden();
});

it('permet à l\'écoutant de prendre une conversation et de répondre', function () {
    User::query()->update(['is_available' => false]);
    $listener = userWithRole(Role::Listener);
    $conversation = app(ListenerQueue::class)->open(userWithRole(Role::Member));

    $this->actingAs($listener)
        ->post("/admin/ecoute/{$conversation->id}/prendre")
        ->assertRedirect("/admin/ecoute/{$conversation->id}");

    $this->actingAs($listener)
        ->post("/admin/ecoute/{$conversation->id}/messages", ['body' => 'Je suis là pour toi.'])
        ->assertRedirect();

    expect($conversation->messages()->where('sender_id', $listener->id)->value('body'))->toBe('Je suis là pour toi.');
});

it('empêche un écoutant de lire la conversation d\'un autre', function () {
    $conversation = app(ListenerQueue::class)->open(userWithRole(Role::Member));
    app(ListenerQueue::class)->assign($conversation, userWithRole(Role::Listener));

    $this->actingAs(userWithRole(Role::Listener))->get("/admin/ecoute/{$conversation->id}")->assertForbidden();
});

it('permet au superviseur de suspendre un compte signalé', function () {
    $member = userWithRole(Role::Member);
    $listener = userWithRole(Role::Listener);
    $conversation = Conversation::create(['member_id' => $member->id, 'listener_id' => $listener->id, 'status' => 'active']);
    $report = Report::create([
        'reporter_id' => $member->id, 'reported_user_id' => $listener->id,
        'conversation_id' => $conversation->id, 'reason' => 'harassment',
    ]);

    $this->actingAs(userWithRole(Role::Supervisor))
        ->post("/admin/signalements/{$report->id}/traitement", ['resolution' => 'suspended'])
        ->assertRedirect();

    expect($listener->fresh()->isSuspended())->toBeTrue()
        ->and($report->fresh()->status->value)->toBe('resolved');
});

it('donne au professionnel son agenda', function () {
    $this->post('/admin/connexion', ['email' => 'pro@sunoogo.local', 'password' => 'password']);

    $this->get('/admin')->assertRedirect('/admin/agenda');
    $this->get('/admin/agenda')->assertOk()->assertSee('Créneaux à venir');
});
