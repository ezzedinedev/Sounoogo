<?php

namespace App\Domain\Listening\Services;

use App\Domain\Identity\Models\User;
use App\Domain\Listening\Enums\AlertStatus;
use App\Domain\Listening\Enums\RiskLevel;
use App\Domain\Listening\Events\EmergencyRaised;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\EmergencyAlert;
use App\Domain\Listening\Models\EmergencyContact;
use App\Domain\Listening\Models\Message;
use Illuminate\Support\Collection;

/**
 * Protocole d'urgence : redirection vers les numéros locaux côté membre,
 * et alerte en temps réel aux superviseurs.
 */
class EmergencyService
{
    public function raise(
        User $member,
        string $source,
        RiskLevel $level = RiskLevel::High,
        ?Conversation $conversation = null,
        ?Message $message = null,
    ): EmergencyAlert {
        // Une seule alerte ouverte par conversation : on la réhausse au besoin.
        $alert = $conversation?->alerts()->where('status', '!=', AlertStatus::Resolved)->first();

        if ($alert !== null) {
            if ($level === RiskLevel::High && $alert->level !== RiskLevel::High) {
                $alert->update(['level' => $level, 'message_id' => $message?->id]);
            }
        } else {
            $alert = EmergencyAlert::create([
                'member_id' => $member->id,
                'conversation_id' => $conversation?->id,
                'message_id' => $message?->id,
                'source' => $source,
                'level' => $level,
                'status' => AlertStatus::Open,
            ]);
        }

        if ($conversation !== null && $conversation->risk_level !== RiskLevel::High) {
            $conversation->update(['risk_level' => $level]);
        }

        EmergencyRaised::dispatch($alert->fresh());

        return $alert;
    }

    /** @return Collection<int, EmergencyContact> */
    public function contactsFor(?string $country): Collection
    {
        $country ??= config('sunoogo.default_country');

        return EmergencyContact::query()
            ->where('country', $country)
            ->where('is_active', true)
            ->orderBy('position')
            ->get();
    }
}
