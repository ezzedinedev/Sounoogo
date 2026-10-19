#!/bin/sh
# Prépare l'application avant chaque processus : caches de configuration, routes et vues.
# Les migrations ne tournent que dans le conteneur « app » (RUN_MIGRATIONS=true), une seule fois par déploiement.
set -e

php artisan optimize --quiet

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force --no-interaction
fi

exec "$@"
