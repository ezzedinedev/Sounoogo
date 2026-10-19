<?php

namespace App\Domain\Consultation\Actions;

use App\Domain\Consultation\Models\ProfessionalProfile;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

/**
 * Crée des créneaux récurrents : certains jours de la semaine, à certaines heures, sur plusieurs
 * semaines. Les créneaux passés ou qui chevaucheraient un créneau existant sont ignorés.
 */
final class CreateSlotSeries
{
    /**
     * @param  list<int>  $weekdays  jours ISO (1 = lundi … 7 = dimanche)
     * @param  list<string>  $times  heures de début « HH:MM »
     * @return int nombre de créneaux créés
     */
    public function __invoke(ProfessionalProfile $profile, Carbon $from, int $weeks, array $weekdays, array $times): int
    {
        $duration = $profile->session_minutes;
        $period = CarbonPeriod::create($from->copy()->startOfDay(), $from->copy()->startOfDay()->addWeeks($weeks)->subDay());

        $existing = $profile->slots()
            ->whereBetween('starts_at', [$period->getStartDate(), $period->getEndDate()->copy()->endOfDay()])
            ->get(['starts_at', 'ends_at']);

        $created = 0;

        foreach ($period as $day) {
            if (! in_array($day->isoWeekday(), $weekdays, true)) {
                continue;
            }

            foreach ($times as $time) {
                $start = Carbon::parse($day->toDateString().' '.$time);
                $end = $start->copy()->addMinutes($duration);

                $overlaps = $existing->contains(fn ($slot) => $slot->starts_at->lt($end) && $slot->ends_at->gt($start));
                if ($start->isPast() || $overlaps) {
                    continue;
                }

                $existing->push($profile->slots()->create(['starts_at' => $start, 'ends_at' => $end]));
                $created++;
            }
        }

        return $created;
    }
}
