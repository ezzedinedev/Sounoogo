<?php

namespace App\Domain\Listening\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Listening\Enums\AlertStatus;
use App\Domain\Listening\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $member_id
 * @property int|null $conversation_id
 * @property int|null $message_id
 * @property string $source
 * @property RiskLevel $level
 * @property AlertStatus $status
 * @property int|null $handled_by
 * @property Carbon|null $acknowledged_at
 * @property Carbon|null $resolved_at
 * @property string|null $notes
 * @property string|null $reason
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'member_id', 'conversation_id', 'message_id', 'source', 'level', 'status',
    'handled_by', 'acknowledged_at', 'resolved_at', 'notes', 'reason',
])]
class EmergencyAlert extends Model
{
    protected $attributes = [
        'level' => 'high',
        'status' => 'open',
    ];

    protected function casts(): array
    {
        return [
            'level' => RiskLevel::class,
            'status' => AlertStatus::class,
            'acknowledged_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    /** @return BelongsTo<Conversation, $this> */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /** @return BelongsTo<User, $this> */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function sourceLabel(): string
    {
        return match ($this->source) {
            'sos' => 'Bouton SOS',
            'keyword' => 'Mots à risque',
            'ai' => 'IA · à confirmer',
            default => ucfirst($this->source),
        };
    }
}
