#!/usr/bin/env bash

set -euo pipefail

export XDEBUG_MODE=off

echo "[devcontainer] Preparing Codespaces workspace..."

git config --global --add safe.directory "$(pwd)" 2>/dev/null || true

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

VENDOR_DIR="${COMPOSER_VENDOR_DIR:-vendor}"

if [[ "$VENDOR_DIR" != "vendor" ]]; then
    mkdir -p "$VENDOR_DIR"
    if [[ ! -w "$VENDOR_DIR" ]]; then
        sudo chown -R "$(id -u)":"$(id -g)" "$VENDOR_DIR" 2>/dev/null || true
    fi
fi

ensure_vendor_link() {
    if [[ "$VENDOR_DIR" != "vendor" && ( ! -e vendor || -L vendor ) ]]; then
        ln -sfn "$VENDOR_DIR" vendor
    fi
}

install_composer_deps() {
    echo "[devcontainer] Installing Composer dependencies..."
    ensure_vendor_link
    composer install --prefer-dist --no-interaction
}

if [[ ! -f "${VENDOR_DIR}/autoload.php" ]]; then
    if ! install_composer_deps; then
        # Windows bind mounts (9p/drvfs) cannot chmod extracted archives.
        echo "[devcontainer] composer install failed; retrying with vendor on native filesystem..."
        rm -rf vendor
        export COMPOSER_VENDOR_DIR="/home/vscode/.cache/${PWD##*/}-vendor"
        mkdir -p "$COMPOSER_VENDOR_DIR"
        VENDOR_DIR="$COMPOSER_VENDOR_DIR"
        install_composer_deps
    fi
else
    ensure_vendor_link
    echo "[devcontainer] Composer dependencies already installed; skipping"
fi

cat <<'EOF'

[devcontainer] Workspace is ready.

Next steps:
  1. php -S 0.0.0.0:8000 -t public
  2. Open the forwarded port for the app preview
  3. Run vendor/bin/phpunit

EOF
