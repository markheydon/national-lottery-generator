# Development Setup and Workflow

This page collects the practical setup and maintenance guidance that is useful for contributors and maintainers.

## Requirements

- PHP 8.3 to 8.5
- Composer
- Git

No database, Docker, or Node.js is required for the core application or local development. Playwright E2E specs in `tests/e2e/` run in GitHub Actions CI on pull requests (see [`.github/workflows/ci.yml`](../.github/workflows/ci.yml)).

## Dev Container / Codespaces (recommended)

Open the repository in a Dev Container (VS Code / Cursor) or GitHub Codespaces. The configuration uses **`mcr.microsoft.com/devcontainers/php:8.5`** as the base image; [devcontainer features](https://containers.dev/features) add GitHub CLI and other maintainer tooling (see [`.devcontainer/devcontainer.json`](../.devcontainer/devcontainer.json)).

There is no custom Dockerfile—rebuild the container after changing `.devcontainer/devcontainer.json` or the lockfile (`Dev Containers: Rebuild Container`).

After the container finishes building, `postCreate` runs [`.devcontainer/setup.sh`](../.devcontainer/setup.sh): Composer install only. Then:

```bash
php -S 0.0.0.0:8000 -t public
```

Open the forwarded port from the Ports panel.

## Local development (no container)

Primary local option — no Docker or Azure required:

```bash
composer install
php -S localhost:8000 -t public
```

Optional: copy `.env.example` to `.env` if you need to tune CSV download timeouts or test URL overrides.

Open http://localhost:8000.

To use Apache or nginx locally instead of the built-in server, point the document root at `public/` (see [deploy/README.md](../deploy/README.md)). Ensure `storage/app/lottery/` is writable.

## Daily workflow

```bash
vendor/bin/phpunit
vendor/bin/pint --test
vendor/bin/pint
```

Open a pull request to run Playwright E2E in CI.

## Code style

The project follows PSR-12 through Laravel Pint (used as a standalone linter, not the Laravel framework):

```bash
vendor/bin/pint --test
vendor/bin/pint
```

On a Windows drive mount (common in local Dev Containers), your working copy may use CRLF line endings while CI checks out LF. That can make `vendor/bin/pint --test` report many `line_ending` issues locally even though CI passes. Trust CI for style checks, or clone the repo onto a native Linux filesystem if you need local Pint to match CI.

## Deployment options

### Local / self-hosted

Anyone can run the app on their own machine or a VPS with PHP 8.3+:

1. `composer install --no-dev` (or `composer install` for a full checkout)
2. Serve the `public/` directory (built-in server, Apache, or nginx)
3. Ensure `storage/app/lottery/` is writable
4. Optional: set `LOTTERY_DOWNLOAD_TIMEOUT` or `LOTTERY_BACKUP_MAX` in `.env` (see `.env.example`)

PHPUnit seeds draw history from `tests/fixtures/lottery/` (see `tests/bootstrap.php`). Live download smoke tests are in the `integration` group (`vendor/bin/phpunit --group integration`).

See [deploy/README.md](../deploy/README.md) for Apache/nginx notes.

### Azure App Service (this project's production)

The application is deployed to Azure App Service with file-based storage. Production does not require Laravel environment variables (`APP_KEY`, `CACHE_DRIVER`, etc.). Optional:

- `LOTTERY_DOWNLOAD_TIMEOUT` (default 30)
- `LOTTERY_BACKUP_MAX` (default 5 timestamped CSV backups per game)

A simple startup command for the web server can be based on the repository's [deploy/nginx-default](../deploy/nginx-default) file:

```bash
cp /home/site/wwwroot/deploy/nginx-default /etc/nginx/sites-available/default && service nginx reload
```

To set the PHP runtime version in Azure:

```bash
az webapp list-runtimes --os linux | grep PHP
az webapp config set \
  --resource-group <your-resource-group> \
  --name <your-app-name> \
  --linux-fx-version "PHP|8.5"
```

In the Azure portal, set the App Service stack to PHP 8.5 where available.

## Contributor workflow

When preparing a change:

1. Create a feature branch.
2. Make the change and keep it focused.
3. Run PHPUnit and Pint locally; rely on CI for Playwright E2E.
4. Open a pull request with a short summary of the change.

## Common issues

### Port 8000 already in use

Use another port:

```bash
php -S localhost:8080 -t public
```

### Storage permissions

If the application cannot write lottery CSV caches:

```bash
sudo chmod -R 775 storage
sudo chown -R "$USER:$USER" storage
```

### CSV download timeout

If lottery data downloads are timing out, increase the timeout in `.env`:

```bash
LOTTERY_DOWNLOAD_TIMEOUT=60
```
