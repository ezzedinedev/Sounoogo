# SUNOOGO

Plateforme de santé mentale — monorepo (FastAPI + React Native + Admin).

## Structure

```
api/       → Backend FastAPI
mobile/    → Application Expo
admin/     → Back-office React
web/       → Site vitrine
packages/  → Code partagé (tokens)
docs/      → Specs & design
```

Voir [STRUCTURE.txt](STRUCTURE.txt) pour le détail.

| App | Stack | Port |
|-----|-------|------|
| API | FastAPI | 8002 |
| Admin | React + Vite | 5173 |
| Web | Next.js 15 | 5174 |
| Mobile | Expo | 8081 |
| Postgres | Docker | 5432 |
| Redis | Docker | 6380 → 6379 |
| MinIO | Docker | 9000 / 9001 |

## Prérequis

- **Python 3.12** (asyncpg incompatible Python 3.14+)
- Node.js 20+
- Docker Desktop

## Démarrage rapide

### 0. Dépendances JS (monorepo)

```bash
npm run install:core          # admin + web + shared
npm run admin:dev             # http://localhost:5173
npm run web:dev               # http://localhost:5174
```

Mobile (Expo, installer séparément si besoin) :

```bash
npm run install:mobile        # ou : cd mobile && npm install
npm run mobile:start
```

> **Windows** : `npm install` à la racine peut échouer sur React Native (`TAR_ENTRY_ERROR`). Utiliser `npm run install:core` pour l’admin ; installer le mobile séparément.

### 1. Infrastructure

```bash
docker compose up -d
```

### 2. API (FastAPI)

```bash
cd api
python -m venv .venv
.venv\Scripts\activate          # Windows
pip install -r requirements.txt
copy .env.example .env
alembic upgrade head
python -m app.seed
uvicorn app.main:app --reload --port 8002
```

- Health : http://localhost:8002/health
- OpenAPI : http://localhost:8002/docs
- Base API : `http://localhost:8002/v1`

**Alternative Docker** :

```bash
docker compose up -d api
```

### 3. Tests API

```bash
cd api
.venv\Scripts\activate
pytest -v
# ou depuis la racine :
npm run api:test
```

### 4. Admin

```bash
cd admin
npm install
copy .env.example .env
npm run dev
```

### 5. Mobile

```bash
cd mobile
npm install
copy .env.example .env
npx expo start
```

## Comptes seed (après `python -m app.seed`)

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@sunoogo.local | admin12345 |
| Superviseur | supervisor@sunoogo.local | super12345 |
| Écoutant | listener@sunoogo.local | listen12345 |
| Pro | pro@sunoogo.local | pro12345 |

## Fonctionnalités implémentées

| Niveau | Fonctionnalités |
|--------|-----------------|
| **S1** | Auth anonyme, register/login, rôles, health |
| **S2** | Chat WebSocket, file écoutants, urgence, modération |
| **S3** | Booking pros, paiements Wave/Mock, back-office |
| **S4** | Formations, leçons, enrollments, abonnements |

## Documentation

- [PRD](docs/PRD.md)
- [Modèle de domaine](docs/domain-model.md)
- [OpenAPI](docs/api/openapi.yaml)
- [Anti-vibecoding](docs/ANTI-VIBECODING.md)
- [Design system](docs/design/DESIGN-SYSTEM.md)
- [UX flows](docs/design/UX-FLOWS.md)
- [Composants UI](docs/design/COMPONENTS.md)
- [ADR-001 Stack FastAPI](docs/adr/001-stack.md)
- [ADR-002 Anonymat](docs/adr/002-anonymat-donnees.md)
- [Legal Sénégal](docs/legal/senegal-checklist.md)

## Workflow contributeur

Voir [CONTRIBUTING.md](CONTRIBUTING.md) — OpenAPI first, specs before code.
