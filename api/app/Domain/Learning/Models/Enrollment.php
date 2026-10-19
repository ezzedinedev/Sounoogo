<?php

namespace App\Domain\Learning\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Learning\Enums\EnrollmentAccess;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $course_id
 * @property EnrollmentAccess $access
 * @property Carbon|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'course_id', 'access', 'completed_at'])]
class Enrollment extends Model
{
    protected function casts(): array
    {
        return [
            'access' => EnrollmentAccess::class,
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Course, $this> */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** @return BelongsToMany<Lesson, $this> */
    public function completedLessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_completions')->withTimestamps();
    }

    public function progress(): int
    {
        $total = $this->course->lessons()->count();

        return $total === 0 ? 0 : (int) round($this->completedLessons()->count() * 100 / $total);
    }
}
