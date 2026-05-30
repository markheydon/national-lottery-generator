#!/usr/bin/env bash

set -euo pipefail

export COREPACK_ENABLE_DOWNLOAD_PROMPT=0
export XDEBUG_MODE=off

echo "[devcontainer] Preparing Codespaces workspace for Laravel Sail..."

if ! command -v docker >/dev/null 2>&1; then
    echo "[devcontainer] Docker CLI is not available inside the devcontainer."
    exit 1
fi

if ! command -v composer >/dev/null 2>&1; then
    echo "[devcontainer] Composer is not available inside the devcontainer."
    exit 1
fi

if ! command -v node >/dev/null 2>&1; then
    echo "[devcontainer] Node.js is not available inside the devcontainer."
    exit 1
fi

if ! command -v corepack >/dev/null 2>&1; then
    echo "[devcontainer] Corepack is not available inside the devcontainer."
    exit 1
fi

corepack enable >/dev/null 2>&1 || true

if [[ ! -f .env ]]; then
    cp .env.example .env
    echo "[devcontainer] Created .env from .env.example"
fi

mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/testing \
    storage/framework/views \
    bootstrap/cache

# Keep directories writable while avoiding executable bits on regular files.
find storage bootstrap/cache -type d -exec chmod ug+rwx {} + 2>/dev/null || true
find storage bootstrap/cache -type f -exec chmod ug+rw {} + 2>/dev/null || true

if [[ ! -d vendor ]]; then
    echo "[devcontainer] Installing Composer dependencies..."
    composer install --prefer-dist --no-interaction
else
    echo "[devcontainer] Composer dependencies already installed; skipping"
fi

if [[ -f package.json ]]; then
    if [[ ! -d node_modules ]]; then
        echo "[devcontainer] Installing JavaScript dependencies..."
        if [[ -f yarn.lock ]]; then
            yarn install --frozen-lockfile
        else
            npm install
        fi
    else
        echo "[devcontainer] JavaScript dependencies already installed; skipping"
    fi
fi

if [[ -f artisan ]] && grep -Eq '^APP_KEY=$' .env; then
    echo "[devcontainer] Generating APP_KEY..."
    php artisan key:generate --force
fi

cat <<'EOF'

[devcontainer] Workspace is ready.

Next steps:
  1. ./vendor/bin/sail up -d
  2. Open the forwarded port for the app preview
  3. Run ./vendor/bin/sail artisan test

EOF