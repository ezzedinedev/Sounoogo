<?php

namespace App\Domain\Listening\Services;

use App\Domain\Identity\Enums\Role;
use App\Domain\Identity\Models\User;
use App\Domain\Listening\Enums\ConversationStatus;
use App\Domain\Listening\Events\ConversationUpdated;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\Message;
use Illuminate\Support\Facades\DB;

/**
 * File d'attente de l'écoute anonyme : un membre ouvre une conversation, elle est
 * attribuée à l'écoutant disponible le moins chargé, ou attend qu'un écoutant la prenne.
 */
class ListenerQueue
{
    public const MAX_ACTIVE_PER_LISTENER = 3;

    public function open(User $member, ?string $topic = null, string $channel = 'chat'): Conversation
    {
        $existing = $member->memberConversations()
            ->where('status', '!=', ConversationStatus::Closed)
            ->latest()
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        $conversation = $member->memberConversations()->create([
            'status' => ConversationStatus::Waiting,
            'topic' => $topic,
            'channel' => $channel,
        ]);

        $this->systemMessage($conversation, 'Bienvenue. Un écoutant formé va te rejoindre. Tu peux commencer à écrire dès maintenant.');

        if ($listener = $this->leastBusyListener()) {
            $this->assign($conversation, $listener);
        } else {
            ConversationUpdated::dispatch($conversation);
        }

        // refresh() conserve wasRecentlyCreated : le contrôleur répond 201 ou 200 selon le cas.
        return $conversation->refresh();
    }

    /** Un écoutant prend la conversation la plus prioritaire de la file. */
    public function takeNext(User $listener): ?Conversation
    {
        $conversation = Conversation::query()
            ->where('status', ConversationStatus::Waiting)
            ->orderByRaw("case risk_level when 'high' then 0 when 'medium' then 1 else 2 end")
            ->oldest()
            ->first();

        return $conversation ? $this->assign($conversation, $listener) : null;
    }

    public function assign(Conversation $conversation, User $listener): Conversation
    {
        $assigned = DB::transaction(function () use ($conversation, $listener) {
            $locked = Conversation::query()->lockForUpdate()->find($conversation->id);

            if ($locked->status !== ConversationStatus::Waiting) {
                return null;
            }

            $locked->update([
                'listener_id' => $listener->id,
                'status' => ConversationStatus::Active,
                'assigned_at' => now(),
            ]);

            return $locked;
        });

        if ($assigned === null) {
            return $conversation->fresh();
        }

        $this->systemMessage($assigned, "{$listener->displayName()} a rejoint la conversation.");
        ConversationUpdated::dispatch($assigned);

        return $assigned;
    }

    public function close(Conversation $conversation, string $reason): Conversation
    {
        $conversation->update([
            'status' => ConversationStatus::Closed,
            'closed_at' => now(),
            'closed_reason' => $reason,
        ]);

        $this->systemMessage($conversation, 'La conversation est terminée. Merci de ta confiance, prends soin de toi.');
        ConversationUpdated::dispatch($conversation);

        return $conversation;
    }

    public function leastBusyListener(): ?User
    {
        return User::role(Role::Listener->value)
            ->where('is_available', true)
            ->whereNull('suspended_at')
            ->withCount(['listenerConversations as active_count' => fn ($q) => $q->where('status', ConversationStatus::Active)])
            ->get()
            ->where('active_count', '<', self::MAX_ACTIVE_PER_LISTENER)
            ->sortBy('active_count')
            ->first();
    }

    private function systemMessage(Conversation $conversation, string $body): Message
    {
        return $conversation->messages()->create(['kind' => 'system', 'body' => $body]);
    }
}
