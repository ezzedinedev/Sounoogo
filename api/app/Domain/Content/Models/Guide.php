<?php

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Guide pratique pour aider un proche (contenu en Markdown).
 *
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string $summary
 * @property string $body
 * @property int $position
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['slug', 'title', 'summary', 'body', 'position', 'published_at'])]
class Guide extends Model
{
    protected $attributes = ['position' => 0];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    /** @param  Builder<self>  $query */
    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')->orderBy('position')->orderBy('id');
    }

    /** Temps de lecture estimé (200 mots par minute). */
    public function readingMinutes(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->body)) / 200));
    }
}
