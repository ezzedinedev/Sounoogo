<?php

namespace App\Domain\Listening\Events;

use App\Domain\Listening\Models\Conversation;
use App\Http\Resources\ConversationResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/** Diffusé à la conversation elle-même et à la file des écoutants. */
class ConversationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Diffusé via la file d'attente, après validation de la transaction : une panne du
     * serveur temps réel ne doit jamais empêcher l'envoi d'un message ou d'une alerte.
     */
    public bool $afterCommit = true;

    public function __construct(public Conversation $conversation) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("conversation.{$this->conversation->id}"),
            new PrivateChannel('listeners'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'conversation.updated';
    }

    public function broadcastWith(): array
    {
        return ['conversation' => (new ConversationResource($this->conversation->loadMissing('listener')))->resolve()];
    }
}
