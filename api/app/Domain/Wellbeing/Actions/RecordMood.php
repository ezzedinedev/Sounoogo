<?php

namespace App\Domain\Wellbeing\Actions;

use App\Domain\Identity\Models\User;
use App\Domain\Wellbeing\Enums\Feeling;
use App\Domain\Wellbeing\Enums\Mood;
use App\Domain\Wellbeing\Models\MoodEntry;

/** Enregistre le check-in du jour ; un nouveau check-in le même jour remplace le précédent. */
final class RecordMood
{
    /** @param  list<Feeling>  $feelings */
    public function __invoke(User $user, Mood $mood, array $feelings = [], ?string $note = null): MoodEntry
    {
        $entry = MoodEntry::query()->where('user_id', $user->id)->whereDate('recorded_on', today())->first()
            ?? new MoodEntry(['user_id' => $user->id, 'recorded_on' => today()]);

        $entry->fill(['mood' => $mood, 'feelings' => $feelings, 'note' => filled($note) ? trim($note) : null])->save();

        return $entry;
    }
}
