# Contribuer à SUNOOGO

Merci de contribuer. Ce projet applique une discipline **anti-vibecoding** : les specs précèdent le code.

## Démarrage

1. Lire **[docs/ANTI-VIBECODING.md](docs/ANTI-VIBECODING.md)** — règles formelles du projet.
2. Lire **[docs/PRD.md](docs/PRD.md)** pour les user stories et critères d'acceptation.
3. Lire **[docs/domain-model.md](docs/domain-model.md)** pour le modèle métier.

## Workflow feature

```
User story (PRD) → domain-model → openapi.yaml → code → tests → PR
```

| Étape | Action |
|-------|--------|
| Spec | User story + critères Given/When/Then dans `docs/PRD.md` |
| Domaine | Entités / règles dans `docs/domain-model.md` si impact métier |
| API | Modifier `docs/api/openapi.yaml` **avant** les routes FastAPI |
| Architecture | ADR dans `docs/adr/` si décision structurante |
| Code | Tranche verticale courante (S1→S4) — stack FastAPI + React Native |
| PR | Checklist dans ANTI-VIBECODING §5 |

## Stack

- **API** : FastAPI (Python) — pas Laravel
- **Mobile** : React Native (Expo)
- **Admin** : React + Vite
- **BDD** : PostgreSQL · **Cache** : Redis

## Interdit

- Code métier avant Phase 0 complète (PRD, domain-model, OpenAPI, ADR)
- Endpoints sans mise à jour OpenAPI
- Écrans sans user story PRD
- Styles ad hoc (utiliser les design tokens)
- Lancement public avant S6 (beta fermée)

## Cursor

Les agents IA du projet chargent `.cursor/rules/sunoogo-anti-vibecoding.mdc` automatiquement.

Questions process : ouvrir une issue ou discuter en PR avant d'implémenter une feature non spécifiée.
