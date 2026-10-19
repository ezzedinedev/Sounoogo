<?php

namespace App\Domain\Learning\Actions;

use App\Domain\Learning\Models\Enrollment;
use App\Domain\Learning\Models\Lesson;

final class CompleteLesson
{
    public function __invoke(Enrollment $enrollment, Lesson $lesson): Enrollment
    {
        abort_unless($lesson->course_id === $enrollment->course_id, 404);

        $enrollment->completedLessons()->syncWithoutDetaching([$lesson->id]);

        if ($enrollment->completed_at === null && $enrollment->progress() === 100) {
            $enrollment->update(['completed_at' => now()]);
        }

        return $enrollment;
    }
}
