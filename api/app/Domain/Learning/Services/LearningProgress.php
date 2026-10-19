<?php

namespace App\Domain\Learning\Services;

use App\Domain\Identity\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Compteurs de progression d'un membre : leçons et formations terminées, jours d'activité
 * consécutifs (une leçon terminée suffit pour compter la journée).
 */
final class LearningProgress
{
    /** @return array{lessons: int, courses: int, current_streak: int, best_streak: int, active_today: bool} */
    public function for(User $user): array
    {
        $days = DB::table('lesson_completions')
            ->join('enrollments', 'enrollments.id', '=', 'lesson_completions.enrollment_id')
            ->where('enrollments.user_id', $user->id)
            ->selectRaw('DATE(lesson_completions.created_at) as day')
            ->distinct()
            ->orderBy('day')
            ->pluck('day')
            ->map(fn (string $day) => Carbon::parse($day)->startOfDay());

        [$current, $best] = $this->streaks($days->all());

        return [
            'lessons' => DB::table('lesson_completions')
                ->join('enrollments', 'enrollments.id', '=', 'lesson_completions.enrollment_id')
                ->where('enrollments.user_id', $user->id)
                ->count(),
            'courses' => $user->enrollments()->whereNotNull('completed_at')->count(),
            'current_streak' => $current,
            'best_streak' => $best,
            'active_today' => $days->last()?->isToday() ?? false,
        ];
    }

    /**
     * Série en cours (encore valable si la dernière activité date d'hier) et meilleure série.
     *
     * @param  list<Carbon>  $days  jours distincts, triés
     * @return array{int, int}
     */
    private function streaks(array $days): array
    {
        $best = 0;
        $run = 0;
        $previous = null;

        foreach ($days as $day) {
            $run = $previous !== null && (int) $previous->diffInDays($day) === 1 ? $run + 1 : 1;
            $best = max($best, $run);
            $previous = $day;
        }

        $current = $previous !== null && (int) $previous->diffInDays(today()) <= 1 ? $run : 0;

        return [$current, $best];
    }
}
