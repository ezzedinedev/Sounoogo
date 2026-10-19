<?php

use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;
use App\Domain\Wellbeing\Models\MoodEntry;
use Illuminate\Support\Facades\DB;

function moodOn(User $user, int $daysAgo, int $mood): void
{
    MoodEntry::create(['user_id' => $user->id, 'recorded_on' => today()->subDays($daysAgo)->toDateString(), 'mood' => $mood]);
}

it('enregistre le check-in du jour et le remplace s\'il est refait', function () {
    actingAsRole(Role::Member);

    $this->postJson('/api/v1/moods', ['mood' => 2, 'feelings' => ['tired', 'anxious'], 'note' => 'Examens demain'])
        ->assertCreated()
        ->assertJsonPath('data.mood_label', 'Difficile')
        ->assertJsonPath('data.feelings', ['tired', 'anxious'])
        ->assertJsonPath('summary.checked_in_today', true);
    expect(DB::table('mood_entries')->value('note'))->not->toContain('Examens');

    $this->postJson('/api/v1/moods', ['mood' => 4])->assertOk()->assertJsonPath('data.mood', 4);

    expect(MoodEntry::count())->toBe(1);
});

it('refuse une humeur ou une émotion inconnue', function () {
    actingAsRole(Role::Member);

    $this->postJson('/api/v1/moods', ['mood' => 9, 'feelings' => ['bored']])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['mood', 'feelings.0']);
});

it('propose de parler à un écoutant après trois jours difficiles', function () {
    $member = actingAsRole(Role::Member);
    moodOn($member, 2, 1);
    moodOn($member, 1, 2);
    moodOn($member, 0, 2);

    $this->getJson('/api/v1/moods')
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('summary.low_streak', 3)
        ->assertJsonPath('summary.streak', 3)
        ->assertJsonPath('summary.suggestion', 'talk');
});

it('propose un exercice après une seule journée difficile', function () {
    $member = actingAsRole(Role::Member);
    moodOn($member, 1, 4);
    moodOn($member, 0, 1);

    $this->getJson('/api/v1/moods')->assertJsonPath('summary.suggestion', 'exercise');
});

it('compte la régularité même sans check-in aujourd\'hui', function () {
    $member = actingAsRole(Role::Member);
    moodOn($member, 3, 4);
    moodOn($member, 2, 4);
    moodOn($member, 1, 5);

    $this->getJson('/api/v1/moods')
        ->assertJsonPath('summary.streak', 3)
        ->assertJsonPath('summary.checked_in_today', false)
        ->assertJsonPath('summary.average', 4.3)
        ->assertJsonPath('summary.suggestion', null);
});

it('ne montre que le journal du membre connecté', function () {
    moodOn(userWithRole(Role::Member), 0, 1);
    actingAsRole(Role::Member);

    $this->getJson('/api/v1/moods')->assertJsonCount(0, 'data')->assertJsonPath('summary.average', null);
});
