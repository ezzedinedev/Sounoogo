<?php

namespace App\Infrastructure\Video;

use App\Domain\Consultation\Contracts\VideoConferencing;

/**
 * Jitsi Meet auto-hébergé. Avec l'authentification JWT activée côté serveur Jitsi
 * (JITSI_APP_ID / JITSI_APP_SECRET), seul un lien signé par SUNOOGO ouvre la salle,
 * et le professionnel en est le modérateur.
 *
 * @see https://github.com/jitsi/lib-jitsi-meet/blob/master/doc/tokens.md
 */
final class JitsiMeet implements VideoConferencing
{
    /** Le jeton reste valable un peu au-delà de la fenêtre d'accès pour couvrir une reconnexion. */
    private const TOKEN_TTL_MINUTES = 120;

    public function __construct(
        private readonly string $baseUrl,
        private readonly ?string $appId = null,
        private readonly ?string $appSecret = null,
    ) {}

    public function isEnabled(): bool
    {
        return $this->baseUrl !== '';
    }

    public function joinUrl(string $room, string $displayName, bool $moderator): string
    {
        $query = $this->appId && $this->appSecret
            ? '?jwt='.$this->token($room, $displayName, $moderator)
            : '';

        // Options d'interface passées dans le fragment (non transmis au serveur).
        $fragment = implode('&', [
            'userInfo.displayName='.rawurlencode(json_encode($displayName, JSON_UNESCAPED_UNICODE) ?: '""'),
            'config.prejoinConfig.enabled=true',
            'config.disableDeepLinking=false',
            'config.startWithVideoMuted=false',
            'config.subject='.rawurlencode('"Consultation SUNOOGO"'),
        ]);

        return "{$this->baseUrl}/{$room}{$query}#{$fragment}";
    }

    private function token(string $room, string $displayName, bool $moderator): string
    {
        $now = time();
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = [
            'aud' => 'jitsi',
            'iss' => $this->appId,
            'sub' => parse_url($this->baseUrl, PHP_URL_HOST),
            'room' => $room,
            'nbf' => $now - 60,
            'exp' => $now + self::TOKEN_TTL_MINUTES * 60,
            'context' => ['user' => ['name' => $displayName, 'moderator' => $moderator]],
        ];

        $segments = array_map(
            fn (array $part) => $this->base64Url((string) json_encode($part, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
            [$header, $payload],
        );
        $segments[] = $this->base64Url(hash_hmac('sha256', implode('.', $segments), (string) $this->appSecret, true));

        return implode('.', $segments);
    }

    private function base64Url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
