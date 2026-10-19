<?php

namespace App\Infrastructure\AI;

use App\Domain\Listening\Contracts\RiskClassifier;
use App\Domain\Listening\Data\RiskAssessment;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\Message;

/** IA désactivée : seuls les mots-clés évaluent le risque. */
final class DisabledRiskClassifier implements RiskClassifier
{
    public function assess(Conversation $conversation, Message $message): ?RiskAssessment
    {
        return null;
    }
}
