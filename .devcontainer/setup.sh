#!/usr/bin/env bash

set -euo pipefail

export XDEBUG_MODE=off

echo "[devcontainer] Preparing Codespaces workspace..."

if ! command -v composer >/dev/null 2>&1; then
    echo "[devcontainer] Composer is not available inside the devcontainer."
    exit 1
fi

if [[ ! -f .env ]]; then
    cp .env.example .env
    echo "[devcontainer] Created .env from .env.example"
fi

mkdir -p storage/app/lottery
chmod -R ug+rwx storage 2>/dev/null || true

if [[ ! -d vendor ]]; then
    echo "[devcontainer] Installing Composer dependencies..."
    composer install --prefer-dist --no-interaction
else
    echo "[devcontainer] Composer dependencies already installed; skipping"
fi

cat <<'EOF'

[devcontainer] Workspace is ready.

Next steps:
  1. php -S 0.0.0.0:8000 -t public
  2. Open the forwarded port for the app preview
  3. Run vendor/bin/phpunit

EOF
