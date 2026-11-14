# Deploy helpers

Helpers for serving the app with a real web server. The application itself is plain PHP with `public/` as the document root — no framework bootstrap.

## Local (recommended for development)

Use PHP’s built-in server (no nginx/Apache required):

```bash
composer install
cp .env.example .env   # optional
php -S localhost:8000 -t public
```

Open http://localhost:8000. See [QUICKSTART.md](../QUICKSTART.md) and [docs/development-setup.md](../docs/development-setup.md).

## Local Apache

Point the vhost document root at `public/`. The committed `public/.htaccess` rewrites non-file requests to `index.php`.

Ensure `storage/app/lottery/` is writable by the web user.

## Local nginx

Use [`nginx-default`](nginx-default) as a starting point. For a local install, change at least:

- `root` → absolute path to this repo’s `public/` directory (not `/home/site/wwwroot/public`)
- `server_name` → `localhost` or your hostname
- `listen` → `80` (or another free port) if you are not using Azure’s 8080

Keep the front-controller `try_files` / PHP-FPM location blocks.

## Azure App Service (production)

### Two workflows

| Workflow | Trigger | Role |
|----------|---------|------|
| `.github/workflows/deploy-azure-webapp.yml` | Push to `main` | Ship PHP — **does not** run Bicep |
| `.github/workflows/deploy-azure-infrastructure.yml` | Manual only | Provision or update Azure from `deploy/bicep/` |

OIDC login uses repository secrets `AZURE_CLIENT_ID`, `AZURE_TENANT_ID`, `AZURE_SUBSCRIPTION_ID`, and variable `AZURE_WEBAPP_NAME`. Details: [docs/azure-infrastructure.md](../docs/azure-infrastructure.md).

### Bicep (`deploy/bicep/`)

- `main.bicep` — orchestrates plan, web app, OIDC identity, and optional monitoring modules.
- `main.bicepparam` — production-aligned names for incremental adoption (`manageAppSettings = false`, `adoptExistingOidcIdentity = true`).
- `main.bicepparam.example` — template for forks (unique app and identity names; creates a new OIDC identity).

CI runs `az bicep build` and `az bicep build-params` when `deploy/bicep/**` changes (`.github/workflows/bicep.yml`).

### Runtime on App Service

Startup command (nginx on port 8080):

```bash
cp /home/site/wwwroot/deploy/nginx-default /etc/nginx/sites-available/default && service nginx reload
```

Optional app settings: `LOTTERY_DOWNLOAD_TIMEOUT` (default 30), `LOTTERY_BACKUP_MAX` (default 5). Do not add legacy `APP_KEY` / Laravel settings — this app is vanilla PHP.
