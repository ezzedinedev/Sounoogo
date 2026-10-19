<?php

use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\DeviceToken;
use App\Domain\Identity\Models\User;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\Message;
use App\Domain\Listening\Notifications\ListenerJoined;
use App\Domain\Listening\Notifications\ListenerReplied;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

/** @return array{0: User, 1: User, 2: Conversation} */
function openedConversation(): array
{
    $listener = userWithRole(Role::Listener, ['is_available' => true]);
    $member = actingAsRole(Role::Member);
    $id = test()->postJson('/api/v1/conversations')->json('data.id');

    return [$member, $listener, Conversation::findOrFail($id)];
}

it('reçoit un message vocal chiffré, lisible par les seuls participants', function () {
    Storage::fake('local');
    [$member, $listener, $conversation] = openedConversation();

    $response = $this->post("/api/v1/conversations/{$conversation->id}/voice", [
        'audio' => UploadedFile::fake()->createWithContent('note.m4a', 'contenu-audio-de-test'),
        'duration' => 42,
    ], ['Accept' => 'application/json'])
        ->assertCreated()
        ->assertJsonPath('message.kind', 'audio')
        ->assertJsonPath('message.body', 'Message vocal (0:42)')
        ->assertJsonPath('message.audio.duration', 42);

    $message = Message::where('kind', 'audio')->sole();
    expect(Storage::disk('local')->get($message->audio_path))->not->toContain('contenu-audio-de-test');

    $this->get($response->json('message.audio.url'))->assertOk()->assertSee('contenu-audio-de-test', false);

    Sanctum::actingAs(userWithRole(Role::Member));
    $this->getJson($response->json('message.audio.url'))->assertForbidden();
});

it('refuse un message vocal trop long', function () {
    [, , $conversation] = openedConversation();

    $this->postJson("/api/v1/conversations/{$conversation->id}/voice", [
        'audio' => UploadedFile::fake()->createWithContent('note.m4a', 'x'),
        'duration' => 500,
    ])->assertUnprocessable()->assertJsonValidationErrors('duration');
});

it('efface les fichiers vocaux avec le compte', function () {
    Storage::fake('local');
    [, , $conversation] = openedConversation();
    $this->post("/api/v1/conversations/{$conversation->id}/voice", ['audio' => UploadedFile::fake()->createWithContent('n.m4a', 'audio'), 'duration' => 5], ['Accept' => 'application/json']);
    $path = Message::where('kind', 'audio')->value('audio_path');

    $this->deleteJson('/api/v1/me')->assertNoContent();

    Storage::disk('local')->assertMissing($path);
});

it('confie la prochaine conversation à l\'écoutant préféré s\'il est disponible', function () {
    [$member, $preferred, $first] = openedConversation();
    $this->postJson("/api/v1/conversations/{$first->id}/close");
    userWithRole(Role::Listener, ['is_available' => true]);

    $this->postJson('/api/v1/me/preferred-listener', ['conversation_id' => $first->id])
        ->assertOk()
        ->assertJsonPath('data.preferred_listener.display_name', $preferred->displayName());

    $this->postJson('/api/v1/conversations')->assertCreated()->assertJsonPath('data.listener.id', $preferred->id);
});

it('ne permet pas de choisir l\'écoutant d\'une conversation d\'un autre membre', function () {
    [, , $conversation] = openedConversation();

    actingAsRole(Role::Member);
    $this->postJson('/api/v1/me/preferred-listener', ['conversation_id' => $conversation->id])->assertNotFound();
});

it('prévient le membre, sans contenu, quand un écoutant le rejoint ou lui répond', function () {
    Notification::fake();
    [$member, $listener, $conversation] = openedConversation();
    Notification::assertSentTo($member, ListenerJoined::class);

    Sanctum::actingAs($listener);
    $this->postJson("/api/v1/conversations/{$conversation->id}/messages", ['body' => 'Je suis là, je t\'écoute.']);

    Notification::assertSentTo($member, ListenerReplied::class, function (ListenerReplied $notification) use ($member) {
        return ! str_contains($notification->toPush($member)->body, 'écoute');
    });
});

it('inscrit et désinscrit un appareil', function () {
    $member = actingAsRole(Role::Member);

    $this->postJson('/api/v1/me/devices', ['token' => 'fcm-123', 'platform' => 'android'])->assertNoContent();
    expect($member->deviceTokens()->count())->toBe(1);

    // Un autre compte se connecte sur le même téléphone : le jeton change de propriétaire.
    $other = actingAsRole(Role::Member);
    $this->postJson('/api/v1/me/devices', ['token' => 'fcm-123', 'platform' => 'android'])->assertNoContent();
    expect(DeviceToken::sole()->user_id)->toBe($other->id);

    $this->deleteJson('/api/v1/me/devices', ['token' => 'fcm-123'])->assertNoContent();
    expect(DeviceToken::count())->toBe(0);
});
