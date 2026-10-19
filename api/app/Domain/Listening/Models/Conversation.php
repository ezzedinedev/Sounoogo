<?php

namespace App\Domain\Listening\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Listening\Enums\ConversationStatus;
use App\Domain\Listening\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $member_id
 * @property int|null $listener_id
 * @property ConversationStatus $status
 * @property string $channel
 * @property string|null $topic
 * @property RiskLevel $risk_level
 * @property Carbon|null $assigned_at
 * @property Carbon|null $closed_at
 * @property string|null $closed_reason
 * @property int|null $member_rating
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'member_id', 'listener_id', 'status', 'channel', 'topic', 'risk_level',
    'assigned_at', 'closed_at', 'closed_reason', 'member_rating',
])]
class Conversation extends Model
{
    protected $attributes = [
        'status' => 'waiting',
        'channel' => 'chat',
        'risk_level' => 'none',
    ];

    protected function casts(): array
    {
        return [
            'status' => ConversationStatus::class,
            'risk_level' => RiskLevel::class,
            'assigned_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    /** @return BelongsTo<User, $this> */
    public function listener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'listener_id');
    }

    /** @return HasMany<Message, $this> */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /** @return HasOne<Message, $this> */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /** @return HasMany<EmergencyAlert, $this> */
    public function alerts(): HasMany
    {
        return $this->hasMany(EmergencyAlert::class);
    }

    public function involves(User $user): bool
    {
        return $user->id === $this->member_id || $user->id === $this->listener_id;
    }

    public function isOpen(): bool
    {
        return $this->status !== ConversationStatus::Closed;
    }
}
