<?php

namespace App\Infrastructure\AI;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Accès minimal à l'API Messages d'Anthropic. Chaque appel force l'usage d'un outil
 * pour obtenir une réponse structurée (JSON validé par le schéma de l'outil).
 */
final class AnthropicClient
{
    /**
     * @param  array{name: string, description: string, input_schema: array<string, mixed>}  $tool
     * @return array<string, mixed> L'entrée passée à l'outil par le modèle.
     *
     * @throws RuntimeException si l'API ne répond pas ou renvoie une réponse inattendue.
     */
    public function callTool(string $model, string $system, string $prompt, array $tool, int $maxTokens = 600): array
    {
        $config = config('sunoogo.ai');

        try {
            $response = Http::baseUrl($config['base_url'])
                ->withHeaders(['x-api-key' => $config['api_key'], 'anthropic-version' => '2023-06-01'])
                ->acceptJson()
                ->timeout($config['timeout'])
                ->post('/v1/messages', [
                    'model' => $model,
                    'max_tokens' => $maxTokens,
                    'system' => $system,
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'tools' => [$tool],
                    'tool_choice' => ['type' => 'tool', 'name' => $tool['name']],
                ])
                ->throw();
        } catch (ConnectionException|RequestException $e) {
            throw new RuntimeException('Appel à Claude impossible : '.$e->getMessage(), previous: $e);
        }

        $call = collect($response->json('content', []))
            ->first(fn ($block) => ($block['type'] ?? null) === 'tool_use' && ($block['name'] ?? null) === $tool['name']);

        if (! is_array($call['input'] ?? null)) {
            throw new RuntimeException('Réponse de Claude inattendue.');
        }

        return $call['input'];
    }
}
