<?php

namespace App\Http\Resources;

use App\Domain\Learning\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Lesson */
class LessonResource extends JsonResource
{
    /** Le contenu n'est exposé que si l'appelant y a accès (inscrit, ou leçon d'aperçu). */
    public bool $withContent = false;

    public function withContent(bool $value = true): static
    {
        $this->withContent = $value;

        return $this;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'position' => $this->position,
            'duration_minutes' => $this->duration_minutes,
            'is_preview' => $this->is_preview,
            'content' => $this->when($this->withContent, $this->content),
            'video_url' => $this->when($this->withContent, $this->video_url),
        ];
    }
}
