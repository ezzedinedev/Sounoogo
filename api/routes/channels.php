<?php

use App\Domain\Identity\Models\User;
use App\Domain\Listening\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('conversation.{conversation}', function (User $user, Conversation $conversation) {
    return $user->can('view', $conversation);
});

Broadcast::channel('listeners', fn (User $user) => $user->can('conversations.listen'));

Broadcast::channel('supervisors', fn (User $user) => $user->can('alerts.manage'));
