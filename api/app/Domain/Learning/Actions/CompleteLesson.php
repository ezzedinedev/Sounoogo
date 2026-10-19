<?php

namespace App\Domain\Learning\Actions;

use App\Domain\Learning\Enums\Badge;
use App\Domain\Learning\Models\Enrollment;
use App\Domain\Learning\Models\Lesson;

final class CompleteLesson
{
    public function __construct(private readonly AwardBadges $awardBadges) {}

    /** @return array{Enrollment, list<Badge>} l'inscription à jour et les jalons obtenus à cette occasion */
    public function __invoke(Enrollment $enrollment, Lesson $lesson): array
    {
        abort_unless($lesson->course_id === $enrollment->course_id, 404);

        $enrollment->completedLessons()->syncWithoutDetaching([$lesson->id]);

        if ($enrollment->completed_at === null && $enrollment->progress() === 100) {
            $enrollment->update(['completed_at' => now()]);
        }

        return [$enrollment, ($this->awardBadges)($enrollment->user)];
    }
}
