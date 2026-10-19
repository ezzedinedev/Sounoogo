<?php

namespace App\Domain\Content\Models;

use App\Domain\Content\Enums\ExerciseCategory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Exercice guidé, utilisable hors ligne : soit des étapes minutées, soit une respiration rythmée.
 * Un enregistrement audio peut l'accompagner (en français, mooré ou dioula).
 *
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string $summary
 * @property ExerciseCategory $category
 * @property int $duration_minutes
 * @property list<array{text: string, seconds: int}>|null $steps
 * @property array{inhale: int, hold: int, exhale: int, rest: int, cycles: int}|null $breathing
 * @property string|null $audio_path
 * @property string $language
 * @property int $position
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['slug', 'title', 'summary', 'category', 'duration_minutes', 'steps', 'breathing', 'audio_path', 'language', 'position', 'published_at'])]
class Exercise extends Model
{
    protected $attributes = [
        'language' => 'fr',
        'position' => 0,
        'duration_minutes' => 3,
    ];

    protected function casts(): array
    {
        return [
            'category' => ExerciseCategory::class,
            'steps' => 'array',
            'breathing' => 'array',
            'published_at' => 'datetime',
        ];
    }

    /** @param  Builder<self>  $query */
    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')->orderBy('position')->orderBy('id');
    }

    public function audioUrl(): ?string
    {
        return $this->audio_path === null ? null : Storage::disk('public')->url($this->audio_path);
    }
}
