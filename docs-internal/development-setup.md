# Development Setup and Workflow

This page collects the practical setup and maintenance guidance that is useful for contributors and maintainers.

## Requirements

- PHP 8.3 to 8.5
- Composer
- Git

No database, Docker, or Node.js is required for the core application. Playwright E2E tests install `@playwright/test` on demand.

## Local development

```bash
composer install
cp .env.example .env
php -S localhost:8000 -t public
```

Open http://localhost:8000.

## Codespaces / devcontainer

The repository includes a `.devcontainer` configuration. After the container finishes building:

```bash
composer install
php -S 0.0.0.0:8000 -t public
```

Open the forwarded port from the Codespaces **Ports** panel.

## Daily workflow

```bash
vendor/bin/phpunit
./vendor/bin/pint --test
./vendor/bin/pint
```

Playwright E2E (optional locally):

```bash
npm init -y
npm install @playwright/test@^1.62.1 --no-save
npx playwright install chromium
npx playwright test
```

## Code style

The project follows PSR-12 through Laravel Pint (used as a standalone linter, not the Laravel framework):

```bash
./vendor/bin/pint --test
./vendor/bin/pint
```

## Deployment and Azure App Service

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
3. Run the test suite and Pint.
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
