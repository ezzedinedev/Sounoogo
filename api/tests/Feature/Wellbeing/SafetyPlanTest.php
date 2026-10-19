<?php

use App\Domain\Identity\Enums\Role;
use App\Domain\Wellbeing\Models\SafetyPlan;
use Illuminate\Support\Facades\DB;

function safetyPlanPayload(array $overrides = []): array
{
    return [
        'warning_signs' => ['Je ne dors plus', '  '],
        'coping_strategies' => ['Marcher au bord du barrage'],
        'distractions' => ['Aller chez ma cousine'],
        'support_contacts' => [['name' => 'Awa', 'phone' => '70 12 34 56']],
        'professional_contacts' => [],
        'safe_environment' => ['Confier mes médicaments à ma mère'],
        'reasons_to_live' => ['Mon petit frère'],
        ...$overrides,
    ];
}

it('renvoie null tant que le plan n\'est pas rempli', function () {
    actingAsRole(Role::Member);

    $this->getJson('/api/v1/safety-plan')->assertOk()->assertJsonPath('data', null);
});

it('enregistre le plan, nettoie les entrées et le chiffre au repos', function () {
    actingAsRole(Role::Member);

    $this->putJson('/api/v1/safety-plan', safetyPlanPayload())
        ->assertOk()
        ->assertJsonPath('data.warning_signs', ['Je ne dors plus'])
        ->assertJsonPath('data.support_contacts.0.phone', '70123456');

    $raw = DB::table('safety_plans')->value('content');
    expect($raw)->not->toContain('barrage');
    $this->getJson('/api/v1/safety-plan')->assertJsonPath('data.reasons_to_live', ['Mon petit frère']);
});

it('remplace la version précédente', function () {
    actingAsRole(Role::Member);
    $this->putJson('/api/v1/safety-plan', safetyPlanPayload());

    $this->putJson('/api/v1/safety-plan', safetyPlanPayload(['reasons_to_live' => ['Mes études']]))->assertOk();

    expect(SafetyPlan::count())->toBe(1);
    $this->getJson('/api/v1/safety-plan')->assertJsonPath('data.reasons_to_live', ['Mes études']);
});

it('valide les contacts', function () {
    actingAsRole(Role::Member);

    $this->putJson('/api/v1/safety-plan', safetyPlanPayload(['support_contacts' => [['name' => 'Awa']]]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('support_contacts.0.phone');
});

it('n\'est lisible que par son auteur', function () {
    actingAsRole(Role::Member);
    $this->putJson('/api/v1/safety-plan', safetyPlanPayload());

    actingAsRole(Role::Member);
    $this->getJson('/api/v1/safety-plan')->assertJsonPath('data', null);
});

it('peut être supprimé', function () {
    actingAsRole(Role::Member);
    $this->putJson('/api/v1/safety-plan', safetyPlanPayload());

    $this->deleteJson('/api/v1/safety-plan')->assertNoContent();

    expect(SafetyPlan::count())->toBe(0);
});
