<?php

namespace App\Domain\Listening\Contracts;

use App\Domain\Listening\Data\RiskAssessment;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\Message;

/**
 * Second regard sur le risque, en complément des mots-clés : lit le message dans son contexte.
 * Renvoie null quand il ne peut pas se prononcer (désactivé, indisponible).
 */
interface RiskClassifier
{
    public function assess(Conversation $conversation, Message $message): ?RiskAssessment;
}
