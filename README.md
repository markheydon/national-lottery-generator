# National Lottery Generator App

## Overview

Just for fun, makes an attempt at 'guessing' the Lotto numbers using a half-arsed bit of logic.

## Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 11.x (LTS)
- **Docker** (optional, recommended for local development via Laravel Sail)

**Note**: This application no longer requires a database. All data is stored using Laravel's file cache and filesystem storage.

## Local Development with Laravel Sail

Laravel Sail provides a simple way to run the application locally with Docker. No need to install PHP or other dependencies on your machine.

### First Time Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/markheydon/national-lottery-generator.git
   cd national-lottery-generator
   ```

2. Install Composer dependencies (requires PHP 8.2+ on host, or use a Docker container):
   ```bash
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php83-composer:latest \
       composer install --ignore-platform-reqs
   ```

3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

4. Start Sail:
   ```bash
   ./vendor/bin/sail up -d
   ```

5. Generate application key:
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

6. Access the application at [http://localhost](http://localhost)

### Daily Development

```bash
# Start the development environment
./vendor/bin/sail up -d

# Run tests
./vendor/bin/sail artisan test

# Stop the environment
./vendor/bin/sail down
```

### Code Style

This project follows [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards. Laravel Pint is configured to enforce these standards.

Run Pint to check code style:
```bash
./vendor/bin/sail pint --test
```

Automatically fix code style issues:
```bash
./vendor/bin/sail pint
```

Or without Sail:
```bash
./vendor/bin/pint --test  # Check only
./vendor/bin/pint         # Fix issues
```

## How It Works

The application uses Laravel's **file cache** and **filesystem storage** to manage lottery data:

- **CSV Downloads**: Lottery draw history CSV files are downloaded from the National Lottery website and stored in `storage/app/lottery/`
- **Caching**: Parsed draw data is cached using Laravel's file cache driver to avoid re-downloading and re-parsing between requests
- **Auto-Refresh**: Downloads are automatically refreshed when files are older than 1 day
- **File Storage**: All data is persisted to disk - no database required

### Storage Locations

- CSV files: `storage/app/lottery/*.csv`
- Parsed JSON: `storage/app/lottery/*.json` (optional, for debugging)
- Cache data: `storage/framework/cache/data/`

## Deployment on Azure App Services

I have this web app deployed on Azure App Services. The following sections describe the configuration required to get it working.

### Nginx Configuration

Supplied [`nginx-default`](nginx-default) file can be used by setting the following in the Configuration->General settings->Startup Command

```
cp /home/site/wwwroot/nginx-default /etc/nginx/sites-available/default && service nginx reload
```

### Laravel Configuration

As with all Laravel apps, the `APP_KEY` environment variable needs to be set in the Configuration->Application settings section.

The following cache and filesystem settings should be configured:

```json
[
    {
        "name": "CACHE_DRIVER",
        "value": "file",
        "slotSetting": false
    },
    {
        "name": "FILESYSTEM_DISK",
        "value": "local",
        "slotSetting": false
    }
]
```