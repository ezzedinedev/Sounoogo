<?php

namespace App\Infrastructure\AI;

use App\Domain\Listening\Contracts\ListenerCopilot;
use App\Domain\Listening\Data\ConversationSummary;
use App\Domain\Listening\Exceptions\CopilotUnavailable;
use App\Domain\Listening\Models\Conversation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

final class ClaudeListenerCopilot implements ListenerCopilot
{
    private const PRINCIPLES = <<<'PROMPT'
        Tu assistes un écoutant bénévole formé, sur une ligne d'écoute anonyme en santé mentale au Burkina Faso.
        Principes de l'écoute active que tu respectes :
        - reformuler et accueillir les émotions, sans juger ni minimiser ;
        - poser des questions ouvertes, une à la fois ;
        - ne pas donner de diagnostic, ni de conseil médical, ni d'avis tranché ;
        - ne jamais demander d'information identifiante (nom, lieu précis, téléphone) ;
        - si des idées suicidaires apparaissent : les nommer avec douceur et clarté, demander s'il y a un plan,
          rappeler qu'un superviseur et les numéros d'urgence existent.
        Ton : chaleureux, simple, tutoiement, phrases courtes, français accessible.
        PROMPT;

    public function __construct(private readonly AnthropicClient $client) {}

    public function suggestReplies(Conversation $conversation): array
    {
        $input = $this->call($conversation, 'propose_replies', 'Propose trois réponses possibles au dernier message du membre, différentes dans leur approche.', [
            'replies' => ['type' => 'array', 'items' => ['type' => 'string'], 'minItems' => 1, 'maxItems' => 3],
        ], ['replies']);

        return collect($input['replies'] ?? [])
            ->map(fn ($reply) => Str::limit(trim((string) $reply), 600))
            ->filter()
            ->take(3)
            ->values()
            ->all();
    }

    public function summarize(Conversation $conversation): ConversationSummary
    {
        $input = $this->call($conversation, 'summarize', 'Résume la conversation pour un écoutant qui la reprend, et liste les points de vigilance.', [
            'summary' => ['type' => 'string', 'description' => 'Trois à cinq phrases.'],
            'watch_points' => ['type' => 'array', 'items' => ['type' => 'string'], 'maxItems' => 4],
        ], ['summary']);

        return new ConversationSummary(
            summary: trim((string) ($input['summary'] ?? '')),
            watchPoints: collect($input['watch_points'] ?? [])->map(fn ($point) => trim((string) $point))->filter()->values()->all(),
        );
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  list<string>  $required
     * @return array<string, mixed>
     */
    private function call(Conversation $conversation, string $tool, string $task, array $properties, array $required): array
    {
        try {
            return $this->client->callTool(
                model: config('sunoogo.ai.copilot_model'),
                system: self::PRINCIPLES,
                prompt: $task."\n\nConversation (du plus ancien au plus récent) :\n\n".Transcript::of($conversation, config('sunoogo.ai.context_messages')),
                tool: [
                    'name' => $tool,
                    'description' => $task,
                    'input_schema' => ['type' => 'object', 'properties' => $properties, 'required' => $required],
                ],
                maxTokens: 900,
            );
        } catch (RuntimeException $e) {
            Log::warning('Copilote indisponible', ['conversation' => $conversation->id, 'error' => $e->getMessage()]);

            throw CopilotUnavailable::failed();
        }
    }
}
