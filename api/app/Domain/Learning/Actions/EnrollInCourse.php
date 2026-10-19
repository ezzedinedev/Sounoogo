<?php

namespace App\Domain\Learning\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Learning\Enums\EnrollmentAccess;
use App\Domain\Learning\Exceptions\CoursePaymentRequired;
use App\Domain\Learning\Models\Course;
use App\Domain\Learning\Models\Enrollment;

final class EnrollInCourse
{
    public function __invoke(User $user, Course $course): Enrollment
    {
        if ($existing = $user->enrollments()->where('course_id', $course->id)->first()) {
            return $existing;
        }

        $access = $this->accessFor($user, $course) ?? throw new CoursePaymentRequired($course);

        return $user->enrollments()->create(['course_id' => $course->id, 'access' => $access]);
    }

    public function accessFor(User $user, Course $course): ?EnrollmentAccess
    {
        if ($course->isFree()) {
            return EnrollmentAccess::Free;
        }

        if ($course->included_in_subscription && $user->activeSubscription()?->plan->includes_courses) {
            return EnrollmentAccess::Subscription;
        }

        return null;
    }
}
