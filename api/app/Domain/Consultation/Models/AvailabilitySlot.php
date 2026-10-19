<?php

namespace App\Domain\Consultation\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $professional_profile_id
 * @property Carbon $starts_at
 * @property Carbon $ends_at
 * @property bool $is_booked
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['professional_profile_id', 'starts_at', 'ends_at', 'is_booked'])]
class AvailabilitySlot extends Model
{
    protected $attributes = [
        'is_booked' => false,
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_booked' => 'boolean',
        ];
    }

    public function scopeOpen(Builder $query): void
    {
        $query->where('is_booked', false)->where('starts_at', '>', now());
    }

    /** @return BelongsTo<ProfessionalProfile, $this> */
    public function professional(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class, 'professional_profile_id');
    }
}
