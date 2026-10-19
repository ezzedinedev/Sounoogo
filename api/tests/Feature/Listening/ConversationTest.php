<?php

use App\Domain\Identity\Enums\Role;
use App\Domain\Listening\Enums\ConversationStatus;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\Message;
use Laravel\Sanctum\Sanctum;

it('attribue la conversation à un écoutant disponible', function () {
    $listener = userWithRole(Role::Listener, ['is_available' => true]);
    actingAsRole(Role::Member);

    $this->postJson('/api/v1/conversations', ['topic' => 'stress'])
        ->assertCreated()
        ->assertJsonPath('data.status', 'active')
        ->assertJsonPath('data.listener.id', $listener->id);
});

it('met la conversation en file d\'attente sans écoutant disponible', function () {
    actingAsRole(Role::Member);

    $this->postJson('/api/v1/conversations')->assertCreated()->assertJsonPath('data.status', 'waiting');
});

it('reprend la conversation en cours au lieu d\'en ouvrir une nouvelle', function () {
    actingAsRole(Role::Member);

    $first = $this->postJson('/api/v1/conversations')->json('data.id');
    $second = $this->postJson('/api/v1/conversations')->json('data.id');

    expect($second)->toBe($first);
});

it('n\'expose jamais l\'identité réelle du membre à l\'écoutant', function () {
    $listener = userWithRole(Role::Listener, ['is_available' => true]);
    $member = actingAsRole(Role::Member, ['name' => 'Nom Réel']);
    $conversationId = $this->postJson('/api/v1/conversations')->json('data.id');

    Sanctum::actingAs($listener);

    $this->getJson("/api/v1/conversations/{$conversationId}")
        ->assertOk()
        ->assertJsonPath('data.member.display_name', $member->pseudonym)
        ->assertJsonMissing(['name' => 'Nom Réel'])
        ->assertJsonMissingPath('data.member.email');
});

it('chiffre les messages au repos', function () {
    userWithRole(Role::Listener, ['is_available' => true]);
    actingAsRole(Role::Member);
    $conversationId = $this->postJson('/api/v1/conversations')->json('data.id');

    $this->postJson("/api/v1/conversations/{$conversationId}/messages", ['body' => 'Je me sens seul ce soir'])
        ->assertCreated()
        ->assertJsonPath('message.body', 'Je me sens seul ce soir');

    $raw = DB::table('messages')->where('conversation_id', $conversationId)->where('kind', 'text')->value('body');

    expect($raw)->not->toContain('seul');
});

it('interdit l\'accès à la conversation d\'un autre membre', function () {
    $other = userWithRole(Role::Member);
    $conversation = Conversation::create(['member_id' => $other->id, 'status' => ConversationStatus::Waiting]);

    actingAsRole(Role::Member);

    $this->getJson("/api/v1/conversations/{$conversation->id}")->assertForbidden();
    $this->postJson("/api/v1/conversations/{$conversation->id}/messages", ['body' => 'hello'])->assertForbidden();
});

it('permet à un écoutant de prendre la conversation la plus prioritaire', function () {
    $member = userWithRole(Role::Member);
    $normal = Conversation::create(['member_id' => $member->id, 'status' => ConversationStatus::Waiting]);
    $urgent = Conversation::create(['member_id' => userWithRole(Role::Member)->id, 'status' => ConversationStatus::Waiting, 'risk_level' => 'high']);

    actingAsRole(Role::Listener);

    $this->getJson('/api/v1/listener/queue')->assertOk()->assertJsonPath('data.0.id', $urgent->id);

    $this->patchJson('/api/v1/listener/availability', ['is_available' => true])
        ->assertOk()
        ->assertJsonPath('assigned_conversation.id', $urgent->id);

    $this->postJson("/api/v1/listener/queue/{$normal->id}/take")->assertOk()->assertJsonPath('data.status', 'active');
});

it('réserve la file d\'attente aux écoutants', function () {
    actingAsRole(Role::Member);

    $this->getJson('/api/v1/listener/queue')->assertForbidden();
});

it('clôt la conversation puis permet de l\'évaluer', function () {
    userWithRole(Role::Listener, ['is_available' => true]);
    actingAsRole(Role::Member);
    $id = $this->postJson('/api/v1/conversations')->json('data.id');

    $this->postJson("/api/v1/conversations/{$id}/rating", ['rating' => 5])->assertConflict();
    $this->postJson("/api/v1/conversations/{$id}/close")->assertOk()->assertJsonPath('data.status', 'closed');
    $this->postJson("/api/v1/conversations/{$id}/messages", ['body' => 'encore là ?'])->assertConflict();
    $this->postJson("/api/v1/conversations/{$id}/rating", ['rating' => 5])->assertOk();
});

it('enregistre un signalement pour la modération', function () {
    userWithRole(Role::Listener, ['is_available' => true]);
    actingAsRole(Role::Member);
    $id = $this->postJson('/api/v1/conversations')->json('data.id');
    $messageId = Message::where('conversation_id', $id)->value('id');

    $this->postJson("/api/v1/conversations/{$id}/reports", ['reason' => 'harassment', 'message_id' => $messageId])
        ->assertCreated();

    $this->assertDatabaseHas('reports', ['conversation_id' => $id, 'reason' => 'harassment', 'status' => 'pending']);
});
