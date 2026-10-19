<?php

use App\Domain\Identity\Enums\Role;
use App\Domain\Learning\Enums\Badge;
use App\Domain\Learning\Models\Certificate;
use App\Domain\Learning\Models\Course;

function completedCourse(): Course
{
    $course = course();
    $user = auth()->user();
    $enrollment = $user->enrollments()->create(['course_id' => $course->id, 'access' => 'free', 'completed_at' => now()]);
    $enrollment->completedLessons()->attach($course->lessons->pluck('id'));

    return $course;
}

it('décerne un jalon à la première leçon, une seule fois', function () {
    $course = course();
    actingAsRole(Role::Member);
    $this->postJson("/api/v1/courses/{$course->slug}/enroll");
    [$first, $second] = $course->lessons;

    $this->postJson("/api/v1/courses/{$course->slug}/lessons/{$first->id}/complete")
        ->assertOk()
        ->assertJsonPath('new_badges.0.code', Badge::FirstLesson->value);

    // Formation terminée à la deuxième leçon : nouveau jalon, pas de doublon du premier.
    $this->postJson("/api/v1/courses/{$course->slug}/lessons/{$second->id}/complete")
        ->assertJsonCount(1, 'new_badges')
        ->assertJsonPath('new_badges.0.code', Badge::FirstCourse->value);
});

it('compte les jours d\'affilée et garde la série jusqu\'au lendemain', function () {
    $course = course();
    $course->lessons()->create(['title' => 'Trois', 'content' => '…', 'position' => 3]);
    $member = actingAsRole(Role::Member);
    $this->postJson("/api/v1/courses/{$course->slug}/enroll");

    $today = today();
    foreach ($course->lessons()->orderBy('position')->get() as $index => $lesson) {
        $this->travelTo($today->copy()->subDays(3 - $index)->addHours(10));
        $this->postJson("/api/v1/courses/{$course->slug}/lessons/{$lesson->id}/complete")->assertOk();
    }
    $this->travelBack();

    // Dernière leçon hier : la série tient toujours, mais rien n'a encore été fait aujourd'hui.
    $this->getJson('/api/v1/me/progress')
        ->assertOk()
        ->assertJsonPath('data.current_streak', 3)
        ->assertJsonPath('data.best_streak', 3)
        ->assertJsonPath('data.active_today', false)
        ->assertJsonPath('data.lessons', 3)
        ->assertJsonPath('data.badges.1.code', Badge::ThreeDayStreak->value)
        ->assertJsonPath('data.badges.1.earned', true)
        ->assertJsonPath('data.badges.2.earned', false);

    expect($member->badges()->pluck('badge')->all())->toContain(Badge::ThreeDayStreak);
});

it('ne délivre l\'attestation qu\'une fois la formation terminée', function () {
    $course = course();
    actingAsRole(Role::Member);
    $this->postJson("/api/v1/courses/{$course->slug}/enroll");

    $this->postJson("/api/v1/courses/{$course->slug}/certificate", ['holder_name' => 'Awa Ouédraogo'])->assertStatus(409);
});

it('délivre une attestation vérifiable et téléchargeable par son seul titulaire', function () {
    actingAsRole(Role::Member);
    $course = completedCourse();

    $code = $this->postJson("/api/v1/courses/{$course->slug}/certificate", ['holder_name' => '  Awa   Ouédraogo '])
        ->assertCreated()
        ->assertJsonPath('data.holder_name', 'Awa Ouédraogo')
        ->json('data.code');

    expect($code)->toMatch('/^[A-HJ-NP-Z2-9]{4}-[A-HJ-NP-Z2-9]{4}-[A-HJ-NP-Z2-9]{4}$/');

    // Une seule attestation par formation : le nom ne change plus.
    $this->postJson("/api/v1/courses/{$course->slug}/certificate", ['holder_name' => 'Autre Nom'])
        ->assertOk()
        ->assertJsonPath('data.code', $code)
        ->assertJsonPath('data.holder_name', 'Awa Ouédraogo');

    $this->get("/api/v1/certificates/{$code}/pdf")->assertOk()->assertHeader('content-type', 'application/pdf');

    actingAsRole(Role::Member);
    $this->getJson("/api/v1/certificates/{$code}/pdf")->assertNotFound();

    $this->get("/attestations/{$code}")
        ->assertOk()
        ->assertSee('Awa Ouédraogo')
        ->assertSee($course->title)
        ->assertSee('noindex', false);
});

it('refuse un nom d\'attestation fantaisiste', function () {
    actingAsRole(Role::Member);
    $course = completedCourse();

    $this->postJson("/api/v1/courses/{$course->slug}/certificate", ['holder_name' => '<script>'])->assertJsonValidationErrors('holder_name');
    expect(Certificate::count())->toBe(0);
});

it('renvoie des erreurs de validation en français', function () {
    actingAsRole(Role::Member);
    $course = completedCourse();

    $this->postJson("/api/v1/courses/{$course->slug}/certificate")
        ->assertJsonPath('errors.holder_name.0', 'Le champ nom est obligatoire.');
});
