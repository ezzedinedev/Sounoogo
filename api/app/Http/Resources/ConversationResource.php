<?php

namespace App\Http\Resources;

use App\Domain\Listening\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Conversation */
class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'channel' => $this->channel,
            'topic' => $this->topic,
            'risk_level' => $this->risk_level->value,
            'member' => new ParticipantResource($this->whenLoaded('member')),
            'listener' => new ParticipantResource($this->whenLoaded('listener')),
            'last_message' => new MessageResource($this->whenLoaded('latestMessage')),
            'member_rating' => $this->member_rating,
            'assigned_at' => $this->assigned_at?->toIso8601String(),
            'closed_at' => $this->closed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
