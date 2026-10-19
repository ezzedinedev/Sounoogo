<?php

namespace App\Domain\Wellbeing\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Wellbeing\Enums\Feeling;
use App\Domain\Wellbeing\Enums\Mood;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Check-in d'humeur : un par membre et par jour.
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon $recorded_on
 * @property Mood $mood
 * @property Collection<int, Feeling>|null $feelings
 * @property string|null $note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'recorded_on', 'mood', 'feelings', 'note'])]
class MoodEntry extends Model
{
    protected function casts(): array
    {
        return [
            'recorded_on' => 'date',
            'mood' => Mood::class,
            'feelings' => AsEnumCollection::of(Feeling::class),
            'note' => 'encrypted',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
