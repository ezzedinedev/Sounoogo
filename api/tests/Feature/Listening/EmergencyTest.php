<?php

use App\Domain\Identity\Enums\Role;
use App\Domain\Listening\Events\EmergencyRaised;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;

it('détecte un message à risque, alerte un superviseur et renvoie les numéros d\'urgence', function () {
    Event::fake([EmergencyRaised::class]);
    userWithRole(Role::Listener, ['is_available' => true]);
    actingAsRole(Role::Member);
    $id = $this->postJson('/api/v1/conversations')->json('data.id');

    $this->postJson("/api/v1/conversations/{$id}/messages", ['body' => "J'ai envie d'en finir, je n'en peux plus"])
        ->assertCreated()
        ->assertJsonPath('risk_level', 'high')
        ->assertJsonPath('emergency_contacts.0.phone', '17');

    Event::assertDispatched(EmergencyRaised::class);
    $this->assertDatabaseHas('emergency_alerts', ['conversation_id' => $id, 'source' => 'keyword', 'level' => 'high', 'status' => 'open']);
    $this->assertDatabaseHas('conversations', ['id' => $id, 'risk_level' => 'high']);
});

it('ne crée qu\'une alerte ouverte par conversation', function () {
    Event::fake([EmergencyRaised::class]);
    userWithRole(Role::Listener, ['is_available' => true]);
    actingAsRole(Role::Member);
    $id = $this->postJson('/api/v1/conversations')->json('data.id');

    $this->postJson("/api/v1/conversations/{$id}/messages", ['body' => 'je suis à bout']);
    $this->postJson("/api/v1/conversations/{$id}/messages", ['body' => 'je veux me suicider']);

    $this->assertDatabaseCount('emergency_alerts', 1);
    $this->assertDatabaseHas('emergency_alerts', ['conversation_id' => $id, 'level' => 'high']);
});

it('n\'analyse pas les messages des écoutants', function () {
    $listener = userWithRole(Role::Listener, ['is_available' => true]);
    actingAsRole(Role::Member);
    $id = $this->postJson('/api/v1/conversations')->json('data.id');

    Sanctum::actingAs($listener);
    $this->postJson("/api/v1/conversations/{$id}/messages", ['body' => 'As-tu des pensées de suicide ?'])
        ->assertJsonPath('risk_level', 'none');

    $this->assertDatabaseCount('emergency_alerts', 0);
});

it('déclenche le bouton SOS', function () {
    actingAsRole(Role::Member);

    $this->postJson('/api/v1/emergency')
        ->assertCreated()
        ->assertJsonCount(3, 'emergency_contacts');

    $this->assertDatabaseHas('emergency_alerts', ['source' => 'sos', 'level' => 'high']);
});
