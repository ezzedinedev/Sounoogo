<?php

namespace App\Domain\Billing\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property int $price
 * @property int $duration_days
 * @property bool $includes_courses
 * @property int $consultation_discount
 * @property array<array-key, mixed>|null $features
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'code', 'name', 'description', 'price', 'duration_days', 'includes_courses',
    'consultation_discount', 'features', 'is_active',
])]
class Plan extends Model
{
    protected function casts(): array
    {
        return [
            'includes_courses' => 'boolean',
            'features' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /** @return HasMany<Subscription, $this> */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
