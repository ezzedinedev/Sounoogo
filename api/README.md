# SUNOOGO — API, back-office et site

Application Laravel 13 qui sert :

- l'**API REST** de l'app mobile (`/api/v1`, documentation OpenAPI générée par Scramble sur `/docs/api`) ;
- le **back-office** de l'équipe (`/admin`) : écoute en temps réel, supervision, alertes, agenda et revenus des
  professionnels, contenus, paiements, versements, rapport d'impact ;
- le **site vitrine** (`/`).

## Installation

```bash
composer install && npm ci && npm run build
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
composer run dev            # http://localhost:8000
php artisan reverb:start    # temps réel
```

## Organisation

Le code métier est rangé par domaine dans `app/Domain` (voir `../STRUCTURE.txt`) : chaque domaine a ses modèles,
enums, **actions** (un cas d'usage par classe invocable), services et notifications. Les services externes
passent par des contrats (`Contracts/`) implémentés dans `app/Infrastructure` :

| Contrat | Implémentation | Activation |
|---|---|---|
| `PaymentGateway` | MoneyFusion, paiement fictif (dev) | `PAYMENT_PROVIDER` |
| `RiskClassifier`, `ListenerCopilot` | Claude (Anthropic) ou version neutre | `SUNOOGO_AI_ENABLED` |
| Canal de notification | Firebase Cloud Messaging HTTP v1 | `FIREBASE_CREDENTIALS` |
| `VideoConferencing` | Jitsi Meet (JWT) | `JITSI_URL` |

Paquets : Sanctum (jetons de l'app), Reverb (temps réel), spatie/laravel-permission (rôles), spatie/laravel-activitylog
(journal), spatie/laravel-query-builder (filtres), spatie/laravel-backup (sauvegardes), dompdf (attestations et
rapports), Sentry (erreurs), Scramble (OpenAPI).

## Tâches planifiées

`php artisan schedule:work` (ou le service `scheduler` de Docker) : rappels de consultation toutes les 5 minutes,
sauvegarde quotidienne (1 h 30), nettoyage et contrôle des sauvegardes.

## Qualité

```bash
vendor/bin/pint            # style
composer analyse           # Larastan
php artisan test --parallel
```
