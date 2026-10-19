<?php

namespace App\Http\Resources;

use App\Domain\Content\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Exercise */
class ExerciseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'summary' => $this->summary,
            'category' => $this->category->value,
            'category_label' => $this->category->label(),
            'duration_minutes' => $this->duration_minutes,
            'steps' => $this->steps ?? [],
            'breathing' => $this->breathing,
            'audio_url' => $this->audioUrl(),
            'language' => $this->language,
            'updated_at' => $this->updated_at,
        ];
    }
}
