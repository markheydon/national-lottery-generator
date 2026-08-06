# Deploy helpers

Helpers for serving the app with a real web server. The application itself is plain PHP with `public/` as the document root — no framework bootstrap.

## Local (recommended for development)

Use PHP’s built-in server (no nginx/Apache required):

```bash
composer install
cp .env.example .env   # optional
php -S localhost:8000 -t public
```

Open http://localhost:8000. See [QUICKSTART.md](../QUICKSTART.md) and [docs-internal/development-setup.md](../docs-internal/development-setup.md).

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

Production deploy is automated by `.github/workflows/deploy-azure-webapp.yml` (`composer install --no-dev`, artifact upload).

A typical App Service startup command copies this nginx config into place:

```bash
cp /home/site/wwwroot/deploy/nginx-default /etc/nginx/sites-available/default && service nginx reload
```

Optional env: `LOTTERY_DOWNLOAD_TIMEOUT` (default 30). No `APP_KEY` or database settings are required.
