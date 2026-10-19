<?php

namespace App\Infrastructure\Push;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Firebase Cloud Messaging, API HTTP v1, authentifiée par compte de service (jeton OAuth signé en RS256).
 */
class FcmSender
{
    private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    public static function enabled(): bool
    {
        return filled(config('sunoogo.push.credentials'));
    }

    /** Renvoie false si le jeton n'est plus valide (application désinstallée) : il faut l'oublier. */
    public function send(string $token, PushMessage $message): bool
    {
        $credentials = $this->credentials();

        $response = Http::withToken($this->accessToken($credentials))
            ->acceptJson()
            ->timeout(10)
            ->post("https://fcm.googleapis.com/v1/projects/{$credentials['project_id']}/messages:send", [
                'message' => array_filter([
                    'token' => $token,
                    'notification' => ['title' => $message->title, 'body' => $message->body],
                    'data' => $message->data ?: null,
                    'android' => array_filter([
                        'collapse_key' => $message->collapseKey,
                        'notification' => ['channel_id' => 'sunoogo_messages'],
                    ]),
                    'apns' => $message->collapseKey ? ['headers' => ['apns-collapse-id' => $message->collapseKey]] : null,
                ]),
            ]);

        if ($response->successful()) {
            return true;
        }

        $status = $response->json('error.details.0.errorCode') ?? $response->json('error.status');
        if (in_array($status, ['UNREGISTERED', 'INVALID_ARGUMENT', 'NOT_FOUND'], true)) {
            return false;
        }

        Log::warning('Notification push non envoyée', ['status' => $response->status(), 'error' => $status]);

        return true;
    }

    /** @param  array{client_email: string, private_key: string, token_uri?: string}  $credentials */
    private function accessToken(array $credentials): string
    {
        return Cache::remember('fcm.access_token', now()->addMinutes(50), function () use ($credentials) {
            $now = time();
            $segments = [
                $this->base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT'], JSON_THROW_ON_ERROR)),
                $this->base64Url(json_encode([
                    'iss' => $credentials['client_email'],
                    'scope' => self::SCOPE,
                    'aud' => $credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token',
                    'iat' => $now,
                    'exp' => $now + 3600,
                ], JSON_THROW_ON_ERROR)),
            ];
            if (! openssl_sign(implode('.', $segments), $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256)) {
                throw new RuntimeException('Signature du jeton Firebase impossible : vérifie la clé du compte de service.');
            }
            $segments[] = $this->base64Url($signature);

            return Http::asForm()
                ->post($credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token', [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => implode('.', $segments),
                ])
                ->throw()
                ->json('access_token');
        });
    }

    /** @return array{project_id: string, client_email: string, private_key: string, token_uri?: string} */
    private function credentials(): array
    {
        $raw = (string) config('sunoogo.push.credentials');
        $json = is_file($raw) ? (string) file_get_contents($raw) : (string) base64_decode($raw, true);

        return json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    }

    private function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
