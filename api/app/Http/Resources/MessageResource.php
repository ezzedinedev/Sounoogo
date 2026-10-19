<?php

namespace App\Http\Resources;

use App\Domain\Listening\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Message */
class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'kind' => $this->kind,
            'body' => $this->body,
            'sender' => $this->sender_id ? [
                'id' => $this->sender_id,
                'display_name' => $this->sender?->displayName(),
            ] : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
