<?php

namespace App\Domain\Billing\Models;

use App\Domain\Billing\Enums\PaymentProvider;
use App\Domain\Billing\Enums\PaymentStatus;
use App\Domain\Identity\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $payable_type
 * @property int $payable_id
 * @property int $amount
 * @property string $currency
 * @property PaymentProvider $provider
 * @property PaymentStatus $status
 * @property string $reference
 * @property string|null $provider_reference
 * @property string|null $checkout_url
 * @property array<array-key, mixed>|null $meta
 * @property Carbon|null $paid_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'user_id', 'payable_type', 'payable_id', 'amount', 'currency', 'provider', 'status',
    'reference', 'provider_reference', 'checkout_url', 'meta', 'paid_at',
])]
class Payment extends Model
{
    protected $attributes = [
        'currency' => 'XOF',
        'status' => 'pending',
    ];

    protected function casts(): array
    {
        return [
            'provider' => PaymentProvider::class,
            'status' => PaymentStatus::class,
            'meta' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return MorphTo<Model, $this> */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isFinal(): bool
    {
        return $this->status !== PaymentStatus::Pending;
    }
}
