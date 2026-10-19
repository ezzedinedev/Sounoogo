<?php

namespace App\Domain\Learning\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Learning\Enums\Badge;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property Badge $badge
 * @property Carbon $awarded_at
 */
#[Fillable(['user_id', 'badge', 'awarded_at'])]
class UserBadge extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'badge' => Badge::class,
            'awarded_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
