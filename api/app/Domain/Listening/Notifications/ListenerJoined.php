<?php

namespace App\Domain\Listening\Notifications;

use App\Domain\Identity\Models\User;
use App\Domain\Listening\Models\Conversation;
use App\Infrastructure\Push\FcmChannel;
use App\Infrastructure\Push\PushMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ListenerJoined extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Conversation $conversation)
    {
        $this->afterCommit();
    }

    /** @return list<class-string> Le canal vérifie lui-même la configuration et les appareils du membre. */
    public function via(User $notifiable): array
    {
        return [FcmChannel::class];
    }

    public function toPush(User $notifiable): PushMessage
    {
        return new PushMessage(
            title: 'Quelqu\'un est là',
            body: 'Tu peux reprendre la conversation.',
            data: ['type' => 'conversation', 'conversation_id' => (string) $this->conversation->id],
            collapseKey: 'conversation-'.$this->conversation->id,
        );
    }
}
