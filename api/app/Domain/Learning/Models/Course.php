<?php

namespace App\Domain\Learning\Models;

use App\Domain\Billing\Contracts\Payable;
use App\Domain\Billing\Models\Payment;
use App\Domain\Learning\Enums\EnrollmentAccess;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string $summary
 * @property string|null $description
 * @property string|null $cover_path
 * @property string $level
 * @property int $price
 * @property bool $included_in_subscription
 * @property Carbon|null $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'slug', 'title', 'summary', 'description', 'cover_path', 'level', 'price',
    'included_in_subscription', 'published_at',
])]
class Course extends Model implements Payable
{
    public const LEVELS = [
        'beginner' => 'Découverte',
        'intermediate' => 'Approfondissement',
        'advanced' => 'Avancé',
    ];

    protected function casts(): array
    {
        return [
            'included_in_subscription' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /** @return HasMany<Lesson, $this> */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('position');
    }

    /** @return HasMany<Enrollment, $this> */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /** @return MorphMany<Payment, $this> */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function isFree(): bool
    {
        return $this->price === 0;
    }

    public function coverUrl(): ?string
    {
        if ($this->cover_path === null) {
            return null;
        }

        return str_starts_with($this->cover_path, 'http') ? $this->cover_path : asset($this->cover_path);
    }

    public function paymentLabel(): string
    {
        return "Formation {$this->title}";
    }

    public function onPaymentSucceeded(Payment $payment): void
    {
        $this->enrollments()->updateOrCreate(
            ['user_id' => $payment->user_id],
            ['access' => EnrollmentAccess::Purchase],
        );
    }
}
