# Development Setup and Workflow

This page collects the practical setup and maintenance guidance that is useful for contributors and maintainers.

## Requirements

- PHP 8.3 to 8.5
- Composer
- Git

No database or Docker is required for the core application. Playwright E2E tests use the committed `package.json` (Node.js is included in the Dev Container).

## Dev Container / Codespaces (recommended)

The repository includes a `.devcontainer` configuration for VS Code, Cursor, or GitHub Codespaces (Docker or Podman).

After the container finishes building, `postCreate` runs Composer install, `npm ci`, and Playwright Chromium install automatically. Then:

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
npm run test:e2e
```

In the Dev Container, Playwright browsers are installed during create. On a host PHP install:

```bash
npm ci
npm run test:e2e:install
npm run test:e2e
```

On a Windows drive mount, if `npm install` fails with `EPERM` / `chmod`, see [QUICKSTART.md](QUICKSTART.md#npm-install-fails-with-eperm-dev-container-on-windows).

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
4. Optional: set `LOTTERY_DOWNLOAD_TIMEOUT` in `.env`

See [deploy/README.md](../deploy/README.md) for Apache/nginx notes.

### Azure App Service (this project's production)

The application is deployed to Azure App Service with file-based storage. Production does not require Laravel environment variables (`APP_KEY`, `CACHE_DRIVER`, etc.). Optional:

- `LOTTERY_DOWNLOAD_TIMEOUT` (default 30)

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
3. Run the test suite, Pint, and Playwright E2E where relevant.
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
