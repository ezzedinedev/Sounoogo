<?php

use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;
use App\Domain\Listening\Jobs\AssessMessageRisk;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\EmergencyAlert;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

const CLAUDE_URL = 'https://api.anthropic.com/v1/messages';

function enableAi(): void
{
    config(['sunoogo.ai.enabled' => true, 'sunoogo.ai.api_key' => 'test-key']);
}

/** Réponse de l'API Messages forçant un outil. */
function claudeToolResponse(string $tool, array $input): array
{
    return ['content' => [['type' => 'tool_use', 'id' => 'toolu_1', 'name' => $tool, 'input' => $input]], 'stop_reason' => 'tool_use'];
}

/** @return array{0: User, 1: User, 2: Conversation} */
function activeConversation(): array
{
    $listener = userWithRole(Role::Listener, ['is_available' => true]);
    $member = actingAsRole(Role::Member);
    $id = test()->postJson('/api/v1/conversations')->json('data.id');

    return [$member, $listener, Conversation::findOrFail($id)];
}

it('ne sollicite pas l\'IA quand elle est désactivée', function () {
    Queue::fake();
    [, , $conversation] = activeConversation();

    $this->postJson("/api/v1/conversations/{$conversation->id}/messages", ['body' => 'Je suis épuisé'])->assertCreated();

    Queue::assertNotPushed(AssessMessageRisk::class);
});

it('alerte un superviseur quand l\'IA perçoit un risque que les mots-clés ont manqué', function () {
    enableAi();
    Http::fake([CLAUDE_URL => Http::response(claudeToolResponse('report_risk', ['level' => 'high', 'reason' => 'Évoque un plan pour ce soir.']))]);
    [$member, , $conversation] = activeConversation();

    $this->postJson("/api/v1/conversations/{$conversation->id}/messages", ['body' => 'Ce soir je vais tout arrêter, j\'ai préparé ce qu\'il faut.'])
        ->assertCreated()
        ->assertJsonPath('risk_level', 'none');

    $alert = EmergencyAlert::sole();
    expect($alert->source)->toBe('ai')
        ->and($alert->reason)->toBe('Évoque un plan pour ce soir.')
        ->and($conversation->fresh()->risk_level->value)->toBe('high');

    // Ni pseudonyme ni identité ne quittent la plateforme.
    Http::assertSent(function (Request $request) use ($member) {
        $prompt = $request['messages'][0]['content'];

        return $request->url() === CLAUDE_URL
            && $request['tool_choice']['name'] === 'report_risk'
            && str_contains($prompt, 'Membre : Ce soir je vais tout arrêter')
            && ! str_contains($prompt, $member->pseudonym);
    });
});

it('ne crée pas d\'alerte quand l\'IA ne voit pas de risque supplémentaire', function () {
    enableAi();
    Http::fake([CLAUDE_URL => Http::response(claudeToolResponse('report_risk', ['level' => 'none', 'reason' => 'Rien d\'inquiétant.']))]);
    [, , $conversation] = activeConversation();

    $this->postJson("/api/v1/conversations/{$conversation->id}/messages", ['body' => 'Ça va un peu mieux aujourd\'hui.']);

    expect(EmergencyAlert::count())->toBe(0);
});

it('continue sans l\'IA si elle est en panne', function () {
    enableAi();
    Http::fake([CLAUDE_URL => Http::response(['error' => ['type' => 'overloaded_error']], 529)]);
    [, , $conversation] = activeConversation();

    $this->postJson("/api/v1/conversations/{$conversation->id}/messages", ['body' => 'Je ne sais plus quoi faire.'])->assertCreated();

    expect(EmergencyAlert::count())->toBe(0);
});

it('propose des réponses à l\'écoutant de la conversation', function () {
    enableAi();
    Http::fake([CLAUDE_URL => Http::response(claudeToolResponse('propose_replies', ['replies' => ['Je t\'entends.', 'Qu\'est-ce qui pèse le plus ?']]))]);
    [, $listener, $conversation] = activeConversation();
    $this->postJson("/api/v1/conversations/{$conversation->id}/messages", ['body' => 'Je me sens seul.']);

    $this->actingAs($listener)
        ->postJson(route('backoffice.copilot.suggestions', $conversation))
        ->assertOk()
        ->assertJsonPath('suggestions', ['Je t\'entends.', 'Qu\'est-ce qui pèse le plus ?']);
});

it('réserve les suggestions à l\'écoutant de la conversation', function () {
    enableAi();
    [, , $conversation] = activeConversation();

    $this->actingAs(userWithRole(Role::Listener))
        ->postJson(route('backoffice.copilot.suggestions', $conversation))
        ->assertForbidden();
});

it('répond clairement quand le copilote n\'est pas activé', function () {
    [, $listener, $conversation] = activeConversation();

    $this->actingAs($listener)
        ->postJson(route('backoffice.copilot.summary', $conversation))
        ->assertStatus(503)
        ->assertJsonPath('message', 'Le copilote n\'est pas activé sur cette plateforme.');
});

it('mesure le délai de première réponse de l\'écoutant', function () {
    [, $listener, $conversation] = activeConversation();
    $this->travel(90)->seconds();

    Sanctum::actingAs($listener);
    $this->postJson("/api/v1/conversations/{$conversation->id}/messages", ['body' => 'Bonjour, je suis là.'])->assertCreated();

    expect($conversation->fresh()->created_at->diffInSeconds($conversation->fresh()->first_response_at))->toBeGreaterThanOrEqual(90);
});
