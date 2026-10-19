<?php

namespace App\Domain\Wellbeing\Services;

use App\Domain\Identity\Models\User;
use App\Domain\Wellbeing\Models\MoodEntry;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Lecture du journal d'humeur : tendance, régularité et suggestion bienveillante.
 * Une humeur basse plusieurs jours d'affilée invite à parler à un écoutant.
 */
final class MoodInsights
{
    /** Jours consécutifs d'humeur basse à partir desquels on propose de parler à quelqu'un. */
    public const LOW_DAYS_BEFORE_TALK = 3;

    /** @return Collection<int, MoodEntry> Du plus ancien au plus récent. */
    public function history(User $user, int $days): Collection
    {
        return MoodEntry::query()
            ->where('user_id', $user->id)
            ->whereDate('recorded_on', '>=', today()->subDays($days - 1))
            ->orderBy('recorded_on')
            ->get();
    }

    /**
     * @param  Collection<int, MoodEntry>  $entries
     * @return array{average: float|null, streak: int, low_streak: int, checked_in_today: bool, suggestion: string|null}
     */
    public function summarize(Collection $entries): array
    {
        $latest = $entries->last();
        $lowStreak = $this->consecutive($entries, fn (MoodEntry $entry) => $entry->mood->isLow());

        return [
            'average' => $entries->isEmpty() ? null : round($entries->avg(fn (MoodEntry $entry) => $entry->mood->value), 1),
            'streak' => $this->consecutive($entries, fn () => true),
            'low_streak' => $lowStreak,
            'checked_in_today' => $latest?->recorded_on->isToday() ?? false,
            'suggestion' => match (true) {
                $lowStreak >= self::LOW_DAYS_BEFORE_TALK => 'talk',
                $latest?->mood->isLow() ?? false => 'exercise',
                default => null,
            },
        ];
    }

    /**
     * Jours consécutifs, en remontant depuis aujourd'hui (ou hier si pas encore de check-in),
     * dont l'entrée vérifie la condition.
     *
     * @param  Collection<int, MoodEntry>  $entries
     */
    private function consecutive(Collection $entries, callable $matches): int
    {
        $byDay = $entries->keyBy(fn (MoodEntry $entry) => $entry->recorded_on->toDateString());
        $day = $byDay->has(today()->toDateString()) ? today() : today()->subDay();
        $count = 0;

        while (($entry = $byDay->get($day->toDateString())) && $matches($entry)) {
            $count++;
            $day = Carbon::parse($day)->subDay();
        }

        return $count;
    }
}
