<?php

namespace App\Domain\Listening\Events;

use App\Domain\Listening\Models\EmergencyAlert;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/** Alerte temps réel aux superviseurs (back-office). */
class EmergencyRaised implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public EmergencyAlert $alert) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('supervisors')];
    }

    public function broadcastAs(): string
    {
        return 'emergency.raised';
    }

    public function broadcastWith(): array
    {
        return [
            'alert' => [
                'id' => $this->alert->id,
                'level' => $this->alert->level->value,
                'source' => $this->alert->source,
                'status' => $this->alert->status->value,
                'conversation_id' => $this->alert->conversation_id,
                'created_at' => $this->alert->created_at?->toIso8601String(),
            ],
        ];
    }
}
