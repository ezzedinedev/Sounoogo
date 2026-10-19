<?php

namespace App\Domain\Listening\Events;

use App\Domain\Listening\Models\Message;
use App\Http\Resources\MessageResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Diffusé via la file d'attente, après validation de la transaction : une panne du
     * serveur temps réel ne doit jamais empêcher l'envoi d'un message ou d'une alerte.
     */
    public bool $afterCommit = true;

    public function __construct(public Message $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("conversation.{$this->message->conversation_id}")];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return ['message' => (new MessageResource($this->message->loadMissing('sender')))->resolve()];
    }
}
