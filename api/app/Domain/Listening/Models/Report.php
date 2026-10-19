<?php

namespace App\Domain\Listening\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Listening\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $reporter_id
 * @property int|null $reported_user_id
 * @property int|null $conversation_id
 * @property int|null $message_id
 * @property string $reason
 * @property string|null $details
 * @property ReportStatus $status
 * @property string|null $resolution
 * @property int|null $moderator_id
 * @property Carbon|null $resolved_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'reporter_id', 'reported_user_id', 'conversation_id', 'message_id', 'reason',
    'details', 'status', 'resolution', 'moderator_id', 'resolved_at',
])]
class Report extends Model
{
    protected $attributes = [
        'status' => 'pending',
    ];

    public const REASONS = [
        'harassment' => 'Harcèlement',
        'inappropriate' => 'Propos inappropriés',
        'identity' => 'Demande d\'informations personnelles',
        'spam' => 'Spam ou publicité',
        'other' => 'Autre',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReportStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /** @return BelongsTo<User, $this> */
    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    /** @return BelongsTo<Conversation, $this> */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /** @return BelongsTo<Message, $this> */
    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    /** @return BelongsTo<User, $this> */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function reasonLabel(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }
}
