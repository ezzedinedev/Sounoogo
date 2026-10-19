# Contribuer à SUNOOGO

## Principes

- **Le métier d'abord** : une règle métier vit dans `app/Domain/<Domaine>` (actions, services, enums) côté API,
  et dans `domain/` (cas d'usage) côté app. Les contrôleurs et les écrans restent minces.
- **Pas de faux contenu** : aucun témoignage, chiffre ou partenaire inventé. Les contenus cliniques (exercices,
  guides, formations) sont relus par l'équipe clinique avant publication.
- **La vie privée par défaut** : pas de donnée personnelle sans nécessité, chiffrement des contenus sensibles,
  textes neutres dans les notifications, rien de sensible dans les journaux ni dans Sentry.
- **Accessibilité** : libellés pour les lecteurs d'écran, contrastes, animations désactivables.

## Avant d'ouvrir une pull request

```bash
# API
cd api && vendor/bin/pint && composer analyse && php artisan test --parallel

# Mobile
cd mobile && dart format lib test && flutter analyze && flutter test
```

Chaque nouvelle règle métier arrive avec ses tests (Pest côté API, `flutter_test` côté app).
Les messages de commit suivent [Conventional Commits](https://www.conventionalcommits.org/fr/) : `feat(listening): …`.

## Textes et traductions

Les textes de l'app passent progressivement par `lib/l10n/app_fr.arb` (voir [docs/TRADUCTIONS.md](docs/TRADUCTIONS.md)).
Les traductions en mooré et en dioula sont confiées à des locuteurs natifs, jamais à une traduction automatique.
