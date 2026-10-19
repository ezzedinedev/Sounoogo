<?php

use App\Domain\Billing\Models\Plan;
use App\Domain\Identity\Enums\Role;

it('n\'affiche que les formations publiées', function () {
    course();
    course(['published_at' => null]);

    $this->getJson('/api/v1/courses')->assertOk()->assertJsonCount(1, 'data');
});

it('inscrit directement à une formation gratuite et suit la progression', function () {
    $course = course();
    actingAsRole(Role::Member);

    $this->postJson("/api/v1/courses/{$course->slug}/enroll")->assertCreated()->assertJsonPath('data.access', 'free');

    [$first, $second] = $course->lessons;
    $this->postJson("/api/v1/courses/{$course->slug}/lessons/{$first->id}/complete")->assertJsonPath('data.progress', 50);
    $this->postJson("/api/v1/courses/{$course->slug}/lessons/{$second->id}/complete")->assertJsonPath('data.progress', 100);

    expect($course->enrollments()->first()->completed_at)->not->toBeNull();
});

it('exige un paiement pour une formation payante sans abonnement', function () {
    $course = course(['price' => 5000]);
    actingAsRole(Role::Member);

    $this->postJson("/api/v1/courses/{$course->slug}/enroll")->assertStatus(402);
});

it('donne accès via l\'abonnement', function () {
    $course = course(['price' => 5000]);
    $member = actingAsRole(Role::Member);
    $member->subscriptions()->create([
        'plan_id' => Plan::firstWhere('code', 'essentiel')->id,
        'status' => 'active', 'starts_at' => now(), 'ends_at' => now()->addMonth(),
    ]);

    $this->postJson("/api/v1/courses/{$course->slug}/enroll")->assertJsonPath('data.access', 'subscription');
});

it('inscrit après l\'achat à l\'unité', function () {
    $course = course(['price' => 5000]);
    actingAsRole(Role::Member);

    $reference = $this->postJson("/api/v1/courses/{$course->slug}/purchase", ['provider' => 'mock'])
        ->assertCreated()
        ->assertJsonPath('data.amount', 5000)
        ->json('data.reference');

    $this->getJson("/api/v1/payments/mock/{$reference}");

    $this->getJson('/api/v1/enrollments')->assertJsonPath('data.0.access', 'purchase');
});

it('protège le contenu des leçons non incluses dans l\'aperçu', function () {
    $course = course(['price' => 5000]);
    actingAsRole(Role::Member);
    [$preview, $locked] = $course->lessons;

    $this->getJson("/api/v1/courses/{$course->slug}/lessons/{$preview->id}")->assertOk()->assertJsonPath('data.content', 'Contenu 1');
    $this->getJson("/api/v1/courses/{$course->slug}/lessons/{$locked->id}")->assertForbidden();
});
