<?php

namespace App\Http\Resources;

use App\Domain\Learning\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Course */
class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'summary' => $this->summary,
            'description' => $this->when($request->routeIs('*.courses.show'), $this->description),
            'cover_url' => $this->coverUrl(),
            'level' => $this->level,
            'level_label' => Course::LEVELS[$this->level] ?? $this->level,
            'price' => $this->price,
            'included_in_subscription' => $this->included_in_subscription,
            'lessons_count' => $this->whenCounted('lessons'),
            'duration_minutes' => $this->whenAggregated('lessons', 'duration_minutes', 'sum'),
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
        ];
    }
}
