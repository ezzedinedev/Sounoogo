<?php

namespace App\Domain\Consultation\Models;

use App\Domain\Identity\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $specialty
 * @property string|null $bio
 * @property string|null $photo_path
 * @property array<array-key, mixed>|null $languages
 * @property array<array-key, mixed>|null $modes
 * @property int $session_price
 * @property int $session_minutes
 * @property string|null $city
 * @property string|null $license_number
 * @property Carbon|null $verified_at
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'user_id', 'title', 'specialty', 'bio', 'photo_path', 'languages', 'modes',
    'session_price', 'session_minutes', 'city', 'license_number', 'verified_at', 'is_active',
])]
class ProfessionalProfile extends Model
{
    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'modes' => 'array',
            'verified_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function scopeListed(Builder $query): void
    {
        $query->where('is_active', true)->whereNotNull('verified_at');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<AvailabilitySlot, $this> */
    public function slots(): HasMany
    {
        return $this->hasMany(AvailabilitySlot::class);
    }

    /** @return HasMany<Booking, $this> */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function photoUrl(): ?string
    {
        if ($this->photo_path === null) {
            return null;
        }

        return str_starts_with($this->photo_path, 'http') ? $this->photo_path : asset($this->photo_path);
    }
}
