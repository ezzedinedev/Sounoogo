<?php

namespace App\Policies;

use App\Domain\Identity\Models\User;
use App\Domain\Listening\Models\Conversation;

class ConversationPolicy
{
    /** Les participants, et les superviseurs pour la supervision clinique. */
    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->involves($user) || $user->can('conversations.supervise');
    }

    public function participate(User $user, Conversation $conversation): bool
    {
        return $conversation->involves($user);
    }

    public function take(User $user, Conversation $conversation): bool
    {
        return $user->can('conversations.listen') && $conversation->listener_id === null;
    }

    public function rate(User $user, Conversation $conversation): bool
    {
        return $user->id === $conversation->member_id;
    }
}
