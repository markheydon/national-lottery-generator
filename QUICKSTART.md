# Quick Start Guide

This is a simplified quick start guide for getting the National Lottery Generator running locally or in GitHub Codespaces.

## Prerequisites

- PHP 8.3 or newer
- Composer
- Git

No database, Docker, or Node.js is required for the core application.

## Installation (4 Steps)

### 1. Clone the Repository

```bash
git clone https://github.com/markheydon/national-lottery-generator.git
cd national-lottery-generator
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Start the Application

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

### Check Code Style

```bash
./vendor/bin/pint --test
```

### Fix Code Style

```bash
./vendor/bin/pint
```

## GitHub Codespaces

If you use Codespaces:

1. Create a new GitHub Codespace for this repository.
2. Run `composer install`.
3. Start the server with `php -S 0.0.0.0:8000 -t public`.
4. Open the forwarded port from the Codespaces **Ports** panel.

## Troubleshooting

### Port 8000 is already in use

Use another port:

```bash
php -S localhost:8080 -t public
```

### `composer install` fails with "Operation not permitted" (Dev Container on Windows)

The workspace is on a Windows drive mount that cannot set file permissions during extraction. Rebuild the Dev Container (the config uses a Docker volume for `vendor/`), or run:

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
