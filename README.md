# SUNOOGO

Plateforme de santé mentale lancée au Burkina Faso : **écoute anonyme et gratuite**, consultations avec des
professionnels partenaires, parcours de confiance en soi, et outils de bien-être (plan de sécurité, journal
d'humeur, exercices guidés, ressources de proximité).

| Dossier | Contenu | Pile |
|---|---|---|
| [`api/`](api/README.md) | API REST `/api/v1`, back-office de l'équipe (`/admin`) et site vitrine | Laravel 13, PHP 8.4, Reverb |
| [`mobile/`](mobile/README.md) | Application Android / iOS | Flutter 3.47, GetX, clean architecture |
| [`brand/`](brand/README.md) | Logo, icônes, couleurs | SVG / PNG |

## Démarrer en local

```bash
# API, back-office et site
cd api
composer install && npm ci && npm run build
cp .env.example .env && php artisan key:generate
php artisan migrate --seed          # données de démonstration hors production
composer run dev                    # serveur, file d'attente et Vite
php artisan reverb:start            # temps réel (autre terminal)

# Application (émulateur Android)
cd mobile
flutter pub get
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1
```

Comptes de démonstration (mot de passe `password`) : `admin@`, `supervisor@`, `listener@`, `pro@sunoogo.local`.

## Déployer avec Docker

```bash
cp .env.example .env              # mot de passe PostgreSQL
cp api/.env.example api/.env      # APP_KEY, APP_URL, mail, MoneyFusion, Firebase…
docker compose up -d --build
```

Services : `web` (nginx), `app` (PHP-FPM, applique les migrations), `queue`, `scheduler`, `reverb`, PostgreSQL et
Redis. Le serveur de visio [Jitsi Meet](https://jitsi.github.io/handbook/docs/devops-guide/devops-guide-docker)
se déploie à part (`JITSI_URL`). La liste des points à vérifier avant l'ouverture est dans
[docs/LANCEMENT.md](docs/LANCEMENT.md).

## Qualité

Chaque pull request passe par GitHub Actions ([`.github/workflows`](.github/workflows)) :

- **API** : Pint, Larastan (niveau 5), Pest en parallèle, construction de l'image Docker ;
- **Mobile** : `dart format`, `flutter analyze`, `flutter test` (dont les règles d'architecture), build Android.

## Confidentialité

- Aucune donnée personnelle n'est nécessaire pour être écouté·e : pseudonyme et code de récupération.
- Messages, notes d'humeur, plan de sécurité et messages vocaux sont **chiffrés** dans la base ou sur le disque.
- Notifications au texte neutre (rien sur l'écran verrouillé), mode discret et verrou de l'app.
- Rapports d'impact uniquement agrégés, petits effectifs masqués.
- Suivi des erreurs (Sentry) sans corps de requête ni donnée personnelle.
