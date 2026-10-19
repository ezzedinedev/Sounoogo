# SUNOOGO — application mobile

Application Flutter de SUNOOGO : écoute anonyme et gratuite, consultations avec des professionnels partenaires,
parcours de confiance en soi et abonnements. Elle consomme l'API Laravel (`../api`, routes `/api/v1`).

## Lancer l'app

```bash
flutter pub get
flutter run \
  --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1 \
  --dart-define=REVERB_HOST=10.0.2.2 --dart-define=REVERB_APP_KEY=sunoogo-key
```

Les valeurs par défaut visent l'API locale vue depuis l'émulateur Android.

## Qualité

```bash
flutter analyze   # lints flutter_lints, 0 avertissement attendu
flutter test      # tests unitaires + règles d'architecture
```

## Architecture

Clean architecture en trois couches, découpée par module fonctionnel. GetX sert uniquement à la
présentation (état réactif, navigation) et à l'injection de dépendances, via les bindings.

```
lib/
├── main.dart                     # démarrage, thème, routes
├── routes/                       # noms de routes et pages GetX
└── app/
    ├── di/app_binding.dart       # racine de composition (dépendances globales)
    ├── core/                     # technique transverse, sans métier
    │   ├── error/                # Failure (scellée) + traduction des erreurs HTTP
    │   ├── network/              # ApiProvider (Dio), ApiException
    │   ├── realtime/             # Reverb (protocole Pusher)
    │   ├── storage/              # jeton dans le stockage chiffré
    │   └── theme/ utils/ widgets/
    ├── shared/                   # noyau métier partagé : membre, offres, numéros d'urgence, session
    └── modules/<module>/
        ├── domain/               # Dart pur
        │   ├── entities/
        │   ├── repositories/     # contrats (abstract interface class)
        │   └── usecases/         # un cas d'usage par classe, méthode call()
        ├── data/
        │   ├── models/           # JSON de l'API → entités
        │   ├── datasources/      # appels HTTP / temps réel
        │   └── repositories/     # implémentations ; ApiException → Failure
        └── presentation/
            ├── bindings/         # câblage : data source → repository → cas d'usage → contrôleur
            ├── controllers/      # état de l'écran, appelle les cas d'usage
            ├── screens/
            └── widgets/
```

Modules : `auth`, `listening` (chat d'écoute temps réel), `professionals`, `bookings`, `courses`,
`subscription`, `payments`, `emergency`, `profile`, `home`, `splash`.

### Règles de dépendance

- `domain` ne dépend que de lui-même, de `shared/domain` et de `core/error` : aucun import de Flutter, GetX ou Dio.
- `data` implémente les contrats du domaine et ne connaît pas la présentation.
- Dans `presentation`, seules les bindings voient la couche `data` ; contrôleurs et écrans passent par les cas d'usage.
- Les erreurs traversent les couches sous forme de `Failure` typées (`NetworkFailure`, `PaymentRequiredFailure`,
  `ValidationFailure`…). Chaque contrôleur en tire un message en français.

Ces règles sont vérifiées par `test/architecture_test.dart`.

### Ajouter un module

1. `domain` : entités, contrat du repository, cas d'usage.
2. `data` : modèle JSON, data source (reçoit `ApiProvider` par constructeur), implémentation du repository avec `guardApi`.
3. `presentation` : contrôleur (cas d'usage injectés par constructeur, `isLoading` et `errorMessage` observables),
   binding, écran construit avec `AppColors` / `AppTextStyles` / `AppSpacing`.
4. Route dans `lib/routes/`, tests des cas d'usage dans `test/modules/<module>/`.
