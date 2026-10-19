<?php

use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;
use App\Domain\Learning\Models\Course;
use Database\Seeders\ReferenceDataSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(fn () => $this->seed([RolesAndPermissionsSeeder::class, ReferenceDataSeeder::class]))
    ->in('Feature');

function userWithRole(Role $role, array $attributes = []): User
{
    return User::factory()->role($role)->create($attributes);
}

function actingAsRole(Role $role, array $attributes = []): User
{
    $user = userWithRole($role, $attributes);
    Sanctum::actingAs($user);

    return $user;
}

/** Formation publiée et gratuite, avec deux leçons (la première en aperçu). */
function course(array $attributes = []): Course
{
    $course = Course::create([
        'slug' => 'test-'.uniqid(),
        'title' => 'Formation test',
        'summary' => 'Résumé',
        'price' => 0,
        'published_at' => now()->subDay(),
        ...$attributes,
    ]);
    $course->lessons()->create(['title' => 'Intro', 'content' => 'Contenu 1', 'position' => 1, 'is_preview' => true]);
    $course->lessons()->create(['title' => 'Suite', 'content' => 'Contenu 2', 'position' => 2]);

    return $course;
}
