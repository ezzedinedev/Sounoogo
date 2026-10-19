<?php

namespace App\Infrastructure\AI;

use App\Domain\Listening\Contracts\RiskClassifier;
use App\Domain\Listening\Data\RiskAssessment;
use App\Domain\Listening\Enums\RiskLevel;
use App\Domain\Listening\Models\Conversation;
use App\Domain\Listening\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

final class ClaudeRiskClassifier implements RiskClassifier
{
    private const SYSTEM = <<<'PROMPT'
        Tu aides l'équipe de supervision d'une ligne d'écoute anonyme en santé mentale (Burkina Faso).
        Évalue le niveau de risque exprimé dans le DERNIER message du membre, en tenant compte du contexte.

        Niveaux :
        - none : pas de signe de danger.
        - medium : détresse importante, désespoir, idées noires sans intention ni plan exprimés.
        - high : idées suicidaires actives, intention, plan ou moyen évoqués, automutilation, violence subie
          ou envisagée, danger immédiat.

        Tiens compte des formulations indirectes, du français parlé au Burkina Faso et des fautes de frappe.
        En cas d'hésitation entre deux niveaux, retiens le plus élevé : un humain vérifiera.
        La raison est destinée au superviseur : une phrase factuelle, sans citer d'élément qui identifierait la personne.
        PROMPT;

    public function __construct(private readonly AnthropicClient $client) {}

    public function assess(Conversation $conversation, Message $message): ?RiskAssessment
    {
        try {
            $input = $this->client->callTool(
                model: config('sunoogo.ai.risk_model'),
                system: self::SYSTEM,
                prompt: "Conversation (du plus ancien au plus récent) :\n\n".Transcript::of($conversation, config('sunoogo.ai.context_messages'), $message),
                tool: [
                    'name' => 'report_risk',
                    'description' => 'Rapporte le niveau de risque du dernier message du membre.',
                    'input_schema' => [
                        'type' => 'object',
                        'properties' => [
                            'level' => ['type' => 'string', 'enum' => ['none', 'medium', 'high']],
                            'reason' => ['type' => 'string', 'description' => 'Une phrase, en français.'],
                        ],
                        'required' => ['level', 'reason'],
                    ],
                ],
                maxTokens: 300,
            );
        } catch (RuntimeException $e) {
            // Le filet des mots-clés reste actif : une panne de l'IA ne bloque rien.
            Log::warning('Évaluation du risque par IA indisponible', ['conversation' => $conversation->id, 'error' => $e->getMessage()]);

            return null;
        }

        $level = RiskLevel::tryFrom((string) ($input['level'] ?? ''));

        return $level === null ? null : new RiskAssessment($level, Str::limit(trim((string) ($input['reason'] ?? '')), 280));
    }
}
