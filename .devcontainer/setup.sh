#!/usr/bin/env bash

set -euo pipefail

export XDEBUG_MODE=off

echo "[devcontainer] Preparing workspace..."

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

run_npm_install() {
    if [[ -f package-lock.json ]]; then
        npm ci --no-fund --no-audit
    else
        npm install --no-fund --no-audit
    fi
}

install_npm_deps() {
    if [[ ! -f package.json ]] || ! command -v npm >/dev/null 2>&1; then
        return 0
    fi

    echo "[devcontainer] Installing npm dependencies..."
    run_npm_install
}

install_playwright_browsers() {
    if [[ ! -f package.json ]] || ! command -v npx >/dev/null 2>&1; then
        return 0
    fi

    if [[ ! -d node_modules/@playwright/test ]]; then
        return 0
    fi

    echo "[devcontainer] Installing Playwright Chromium..."
    npx playwright install --with-deps chromium
}

if [[ ! -d node_modules/@playwright/test ]]; then
    if ! install_npm_deps; then
        # Windows bind mounts (9p/drvfs) cannot chmod into node_modules.
        echo "[devcontainer] npm install failed; retrying on native filesystem..."
        rm -rf node_modules
        NPM_PREFIX="/home/vscode/.cache/${PWD##*/}-npm"
        mkdir -p "$NPM_PREFIX"
        cp package.json "$NPM_PREFIX/"
        if [[ -f package-lock.json ]]; then
            cp package-lock.json "$NPM_PREFIX/"
            npm ci --prefix "$NPM_PREFIX" --no-fund --no-audit
        else
            npm install --prefix "$NPM_PREFIX" --no-fund --no-audit
        fi
        ln -sfn "$NPM_PREFIX/node_modules" node_modules
    fi
else
    echo "[devcontainer] npm dependencies already installed; skipping"
fi

install_playwright_browsers

cat <<'EOF'

[devcontainer] Workspace is ready.

Next steps:
  1. php -S 0.0.0.0:8000 -t public
  2. Open the forwarded port for the app preview
  3. vendor/bin/phpunit
  4. npm run test:e2e

EOF
