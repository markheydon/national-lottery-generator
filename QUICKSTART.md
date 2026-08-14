# Quick Start Guide

This is a simplified quick start guide for getting the National Lottery Generator running locally or in a Dev Container / Codespaces.

## Recommended: Dev Container / Codespaces

Works in VS Code, Cursor, or GitHub Codespaces with Docker or Podman.

1. Open the repository in a Dev Container (or create a GitHub Codespace).
2. Wait for the container to finish building — `postCreate` installs Composer deps, npm deps, and Playwright Chromium automatically.
3. Start the server:

```bash
php -S 0.0.0.0:8000 -t public
```

4. Open the forwarded port for **8000** from the Ports panel.

Run tests without extra setup:

```bash
vendor/bin/phpunit
npm run test:e2e
```

## Local PHP (no container)

### Prerequisites

- PHP 8.3 or newer
- Composer
- Git

No database or Node.js is required for the core application.

### Installation (4 Steps)

#### 1. Clone the Repository

```bash
git clone https://github.com/markheydon/national-lottery-generator.git
cd national-lottery-generator
```

#### 2. Install Dependencies

```bash
composer install
```

#### 3. Start the Application

```bash
php -S localhost:8000 -t public
```

## Access the Application

Open your browser and go to: **http://localhost:8000**

Done. The application is now running.

## Common Commands

### Run Tests

```bash
vendor/bin/phpunit
```

### Run Playwright E2E

Requires Node.js. On a host install:

```bash
npm ci
npm run test:e2e:install
npm run test:e2e
```

In the Dev Container, E2E is ready after create — run `npm run test:e2e` only.

### Check Code Style

```bash
vendor/bin/pint --test
```

### Fix Code Style

```bash
vendor/bin/pint
```

## Troubleshooting

### Port 8000 is already in use

Use another port:

```bash
php -S localhost:8080 -t public
```

### Pint reports many issues (Dev Container on Windows)

If the repo is on a Windows drive mount, your working copy may use CRLF line endings while CI checks out LF. That makes `vendor/bin/pint --test` report many `line_ending` issues locally even though CI passes. Trust CI for style checks, or clone onto a native Linux filesystem if you need local Pint to match CI.

### npm install fails with EPERM (Dev Container on Windows)

The workspace is on a Windows drive mount that cannot set file permissions in `node_modules/`. Rebuild the Dev Container (the config uses a Docker volume for `node_modules/`), or run:

```bash
rm -rf node_modules
mkdir -p /home/vscode/.cache/national-lottery-generator-npm
cp package.json package-lock.json /home/vscode/.cache/national-lottery-generator-npm/
npm ci --prefix /home/vscode/.cache/national-lottery-generator-npm
ln -sfn /home/vscode/.cache/national-lottery-generator-npm/node_modules node_modules
npm run test:e2e:install
npm run test:e2e
```

### `composer install` fails with "Operation not permitted" (Dev Container on Windows)

The workspace is on a Windows drive mount that cannot set file permissions during extraction. Rebuild the Dev Container (the config uses a Docker volume for Composer packages at `/home/vscode/.composer-vendor`), or run:

```bash
rm -rf vendor
COMPOSER_VENDOR_DIR=/home/vscode/.cache/national-lottery-generator-vendor composer install
ln -sfn /home/vscode/.cache/national-lottery-generator-vendor vendor
```

### Optional environment file

Copy `.env.example` to `.env` only if you need to tune `LOTTERY_DOWNLOAD_TIMEOUT` or test URL overrides. The app runs without it.

### Permission errors in `storage/`

Ensure the lottery cache directory is writable:

```bash
chmod -R 775 storage
```

### Generate page is slow on first visit

The app downloads draw-history CSV files from the National Lottery API on first use. Subsequent visits use the cached files in `storage/app/lottery/` for up to 24 hours.

## What's Next?

- **Read the full [README.md](README.md)** to understand how the application works
- **Serve with Apache/nginx locally** if you prefer: [deploy/README.md](deploy/README.md)
- **Check [CONTRIBUTING.md](CONTRIBUTING.md)** if you want to contribute
- **Explore the code** in the `src/` directory
- **Run the tests** to see everything working: `vendor/bin/phpunit`

## Need More Help?

- Browse [existing issues](https://github.com/markheydon/national-lottery-generator/issues)
- Open a new issue if you're stuck

---

**Happy lottery number generating!**
