<?php

namespace App\Infrastructure\AI;

use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\Message;

/**
 * Fil de conversation transmis au modèle. Seuls les rôles apparaissent (« Membre », « Écoutant ») :
 * jamais de pseudonyme, de nom ni d'identifiant.
 */
final class Transcript
{
    public static function of(Conversation $conversation, int $limit, ?Message $upTo = null): string
    {
        return $conversation->messages()
            ->where('kind', 'text')
            ->when($upTo, fn ($query) => $query->where('id', '<=', $upTo->id))
            ->latest('id')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(fn (Message $message) => ($message->sender_id === $conversation->member_id ? 'Membre' : 'Écoutant').' : '.$message->body)
            ->implode("\n");
    }
}
