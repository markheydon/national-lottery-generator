# National Lottery Generator App

## Overview

Just for fun, makes an attempt at 'guessing' the Lotto numbers using a half-arsed bit of logic.

## Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 11.x (LTS)
- **Docker** (optional, recommended for local development via Laravel Sail)

## Local Development with Laravel Sail

Laravel Sail provides a simple way to run the application locally with Docker. No need to install PHP, MySQL, or other dependencies on your machine.

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

6. Run migrations and seed the database:
   ```bash
   ./vendor/bin/sail artisan migrate:fresh --seed
   ```

7. Access the application at [http://localhost](http://localhost)

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

## Deployment on Azure App Services

I have this web app deployed on Azure App Services. The following sections describe the configuration required to get it working.

### Nginx Configuration

Supplied [`nginx-default`](nginx-default) file can be used by setting the following in the Configuration->General settings->Startup Command

```
cp /home/site/wwwroot/nginx-default /etc/nginx/sites-available/default && service nginx reload
```

### Database Configuration (Azure MySQL)

The following environment variables need to be set in the Configuration->Application settings section to use Azure MySQL:

```
[
    {
        "name": "DB_CONNECTION",
        "value": "azure-mysql",
        "slotSetting": false
    },
    {
        "name": "MYSQL_ATTR_SSL_CA",
        "value": "/home/site/wwwroot/ssl/DigiCertGlobalRootCA.crt.pem",
        "slotSetting": false
    }
]
```

The `DB_CONNECTION` value is used in the `config/database.php` file to determine which database connection to use, in this case, the `azure-mysql` connection. The `azure-mysql` connection is defined in the `config/database.php` file to use the various AZURE_MYSQL_* environment variables that get created when using the Azure Marketplace Web App + Database offer.

The SSL certificate is required to connect to Azure MySQL. The certificate is in the `ssl` folder in the root of the application.

### Laravel Configuration

As with all Laravel apps, the `APP_KEY` environment variable needs to be set in the Configuration->Application settings section.

Additionally, once the database is setup, the following command need to be run from SSH in the `wwwroot` folder of the application to create the tables and seed the database with the pre-set list of games.

```
php artisan migrate:fresh --seed
```