<?php

namespace App\Domain\Listening\Models;

use App\Domain\Identity\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $conversation_id
 * @property int|null $sender_id
 * @property string $kind
 * @property string $body
 * @property string|null $audio_path
 * @property string|null $audio_mime
 * @property int|null $audio_duration
 * @property bool $is_flagged
 * @property Carbon|null $read_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['conversation_id', 'sender_id', 'kind', 'body', 'audio_path', 'audio_mime', 'audio_duration', 'is_flagged', 'read_at'])]
class Message extends Model
{
    protected $attributes = [
        'kind' => 'text',
        'is_flagged' => false,
    ];

    protected function casts(): array
    {
        return [
            // Chiffré au repos avec APP_KEY : un accès à la base ne suffit pas à lire les échanges.
            'body' => 'encrypted',
            'is_flagged' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function isVoice(): bool
    {
        return $this->kind === 'audio' && $this->audio_path !== null;
    }

    /** @return BelongsTo<Conversation, $this> */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /** @return BelongsTo<User, $this> */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
