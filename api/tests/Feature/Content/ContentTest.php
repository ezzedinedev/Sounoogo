<?php

use App\Domain\Content\Models\Exercise;
use App\Domain\Content\Models\Guide;
use App\Domain\Content\Models\SupportResource;
use App\Domain\Identity\Enums\Role;
use App\Http\Controllers\Backoffice\ExerciseController;
use Database\Seeders\ContentSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('sert les exercices publiés en entier, pour un usage hors ligne', function () {
    $this->seed(ContentSeeder::class);
    Exercise::first()->update(['published_at' => null]);

    $this->getJson('/api/v1/exercises')
        ->assertOk()
        ->assertJsonCount(Exercise::count() - 1, 'data')
        ->assertJsonPath('data.0.breathing.inhale', 4)
        ->assertJsonPath('data.1.category_label', 'Ancrage')
        ->assertJsonStructure(['data' => [['slug', 'title', 'steps', 'audio_url']]]);
});

it('liste les guides sans leur texte, puis le donne à la lecture', function () {
    $this->seed(ContentSeeder::class);
    $guide = Guide::first();

    $this->getJson('/api/v1/guides')->assertOk()->assertJsonMissingPath('data.0.body');
    $this->getJson("/api/v1/guides/{$guide->slug}")->assertOk()->assertJsonPath('data.body', $guide->body);
});

it('filtre l\'annuaire par ville et ne montre que les ressources visibles', function () {
    SupportResource::create(['name' => 'Centre A', 'category' => 'health_center', 'city' => 'Ouagadougou']);
    SupportResource::create(['name' => 'Centre B', 'category' => 'association', 'city' => 'Bobo-Dioulasso']);
    SupportResource::create(['name' => 'Centre masqué', 'category' => 'association', 'city' => 'Ouagadougou', 'is_active' => false]);

    $this->getJson('/api/v1/resources?city=Ouagadougou')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Centre A')
        ->assertJsonPath('cities', ['Bobo-Dioulasso', 'Ouagadougou']);
});

it('lit les étapes saisies ligne par ligne dans le back-office', function () {
    expect(ExerciseController::parseSteps("Inspire lentement | 20\n\nExpire | abc\nRegarde autour de toi"))->toBe([
        ['text' => 'Inspire lentement', 'seconds' => 20],
        ['text' => 'Expire', 'seconds' => 30],
        ['text' => 'Regarde autour de toi', 'seconds' => 30],
    ]);
});

it('permet au superviseur de créer un exercice avec son audio', function () {
    Storage::fake('public');

    $this->actingAs(userWithRole(Role::Supervisor))
        ->post(route('backoffice.exercises.store'), [
            'title' => 'Respirer en mooré',
            'summary' => 'Une respiration guidée en mooré.',
            'category' => 'breathing',
            'duration_minutes' => 3,
            'language' => 'mos',
            'mode' => 'breathing',
            'breathing' => ['inhale' => 4, 'hold' => 0, 'exhale' => 6, 'rest' => 0, 'cycles' => 10],
            'audio' => UploadedFile::fake()->create('guide.mp3', 500, 'audio/mpeg'),
            'published' => '1',
        ])
        ->assertRedirect();

    $exercise = Exercise::sole();
    expect($exercise->breathing['exhale'])->toBe(6)->and($exercise->steps)->toBeNull();
    Storage::disk('public')->assertExists($exercise->audio_path);
});

it('réserve la gestion des contenus à l\'équipe habilitée', function () {
    $this->actingAs(userWithRole(Role::Listener))->get(route('backoffice.exercises.index'))->assertForbidden();
});
