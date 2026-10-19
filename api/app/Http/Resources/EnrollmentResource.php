<?php

namespace App\Http\Resources;

use App\Domain\Learning\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Enrollment */
class EnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'access' => $this->access->value,
            'progress' => $this->progress(),
            'completed_lesson_ids' => $this->completedLessons()->pluck('lessons.id'),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'course' => new CourseResource($this->whenLoaded('course')),
        ];
    }
}
