# National Lottery Generator App

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](LICENSE)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com/docs/12.x)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-purple.svg)](https://www.php.net/)

## Overview

Just for fun, makes an attempt at 'guessing' the Lotto numbers using a half-arsed bit of logic.

This is a Laravel-based web application that analyzes historical UK National Lottery draw data and generates number predictions. While the algorithm is playful rather than statistical, it demonstrates file-based data caching, CSV parsing, and Laravel best practices.

## Features

- 🎲 **Number Generation**: Analyzes historical lottery draw data to generate predictions
- 📊 **Multiple Games**: Supports Lotto, EuroMillions, and Thunderball
- 💾 **No Database**: Uses file-based caching and storage for simplicity
- 🔄 **Auto-Update**: Automatically downloads latest draw data from the National Lottery website
- 🐳 **Docker Ready**: Includes Laravel Sail for easy local development
- ☁️ **Azure Deployment**: Pre-configured for Azure App Services deployment

## Table of Contents

- [Requirements](#requirements)
- [Local Development with Laravel Sail](#local-development-with-laravel-sail)
  - [First Time Setup](#first-time-setup)
  - [Daily Development](#daily-development)
  - [Code Style](#code-style)
- [How It Works](#how-it-works)
  - [Storage Locations](#storage-locations)
  - [Sample CSV Files for Testing](#sample-csv-files-for-testing)
- [Deployment on Azure App Services](#deployment-on-azure-app-services)
  - [Nginx Configuration](#nginx-configuration)
  - [Laravel Configuration](#laravel-configuration)
  - [PHP Runtime Configuration](#php-runtime-configuration)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [Support](#support)
- [License](#license)
- [Acknowledgments](#acknowledgments)

## Requirements

- **PHP**: 8.2 or higher (8.4 recommended for Azure)
- **Laravel**: 12.x (LTS)
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
       laravelsail/php84-composer:latest \
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

### Sample CSV Files for Testing

The repository includes sample CSV files in `storage/app/lottery-data/` directory:

- `lotto-draw-history.csv` - Sample Lotto draw history data
- `euromillions-draw-history.csv` - Sample EuroMillions draw history data
- `thunderball-draw-history.csv` - Sample Thunderball draw history data

**Purpose**: These sample files are included in the repository to enable unit tests to run without requiring a network connection to download live data. The unit tests use these files through the "legacy data path" fallback mechanism in the `Downloader` class.

**Important Notes**:
- These are static sample files and not updated automatically
- They contain a small subset of historical draw data for testing purposes only
- During runtime, the application downloads fresh data from the National Lottery website
- Downloaded files are stored in `storage/app/lottery/` (not `lottery-data/`)
- Backup files with timestamps (e.g., `lotto-draw-history-YYYYMMDDHHMMSS.csv`) are created automatically when downloads occur and should not be committed to the repository

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

### PHP Runtime Configuration

This application requires **PHP 8.2 or higher** (PHP 8.4 recommended for Azure). Azure App Service for Linux supports PHP 8.4 in most regions.

**Note**: PHP version availability varies by Azure region. Use the CLI command below to check which PHP versions are available in your region.

To set the PHP runtime version for your Azure Web App:

#### Option 1: Using Azure CLI

```bash
# List available PHP runtimes in your region
az webapp list-runtimes --os linux | grep PHP

# Set PHP 8.4 runtime (replace with your resource group and app name)
az webapp config set \
  --resource-group <your-resource-group> \
  --name <your-app-name> \
  --linux-fx-version "PHP|8.4"

# Verify the configuration
az webapp config show \
  --resource-group <your-resource-group> \
  --name <your-app-name> \
  --query linuxFxVersion
```

#### Option 2: Using Azure Portal

1. Navigate to your App Service in the Azure Portal
2. Go to **Configuration** → **General settings**
3. Set **Stack** to "PHP"
4. Set **Major version** to "8.4" (or highest available in your region)
5. Click **Save**

#### References

- [Laravel 12 Release Notes](https://laravel.com/docs/12.x/releases) - Supports PHP 8.2-8.5
- [Laravel 12 Upgrade Guide](https://laravel.com/docs/12.x/upgrade)
- [Azure App Service PHP Support](https://learn.microsoft.com/en-us/azure/app-service/configure-language-php)
- [Azure Language Support Policy](https://learn.microsoft.com/en-us/azure/app-service/language-support-policy)

## Troubleshooting

### Common Issues

#### Port 80 Already in Use

If you see "port 80 is already in use" when starting Sail:

```bash
# Stop any conflicting services
sudo service apache2 stop  # If Apache is running
sudo service nginx stop    # If Nginx is running

# Or change the port in docker-compose.yml
# Then restart Sail
./vendor/bin/sail down
./vendor/bin/sail up -d
```

#### Permission Issues with Storage Directory

If you encounter permission errors:

```bash
# Fix storage permissions
sudo chmod -R 775 storage
sudo chown -R $USER:$USER storage

# Or with Sail
./vendor/bin/sail exec laravel.test chmod -R 775 storage
```

#### Tests Failing

If tests fail on fresh install:

```bash
# Clear all caches
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan view:clear

# Run tests again
./vendor/bin/sail artisan test
```

#### CSV Download Timeout

If lottery data downloads are timing out:

```bash
# Increase timeout in .env
LOTTERY_DOWNLOAD_TIMEOUT=60

# Then restart
./vendor/bin/sail down
./vendor/bin/sail up -d
```

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details on:

- Setting up your development environment
- Coding standards and style guide
- Testing requirements
- Pull request process
- Reporting issues

### Quick Contribution Guidelines

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes following PSR-12 standards
4. Run tests and Pint: `./vendor/bin/sail artisan test && ./vendor/bin/sail pint`
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to your branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

## Support

### Getting Help

- **Documentation**: Check the [docs/](docs/) folder for detailed guides
- **Issues**: Search [existing issues](https://github.com/markheydon/national-lottery-generator/issues) or create a new one
- **Laravel Docs**: Consult the [Laravel 12 documentation](https://laravel.com/docs/12.x)

### Reporting Security Vulnerabilities

If you discover a security vulnerability, please email the maintainer directly rather than opening a public issue.

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE](LICENSE) file for details.

This means you are free to:
- Use the software for any purpose
- Change the software to suit your needs
- Share the software with others
- Share the changes you make

Under the conditions that:
- You must share your changes under the same license
- You must include the license and copyright notice
- You must state significant changes made to the software

## Acknowledgments

- **Laravel Framework**: Built with [Laravel](https://laravel.com/), the elegant PHP framework
- **National Lottery**: Data sourced from the [UK National Lottery website](https://www.national-lottery.co.uk/)
- **Community**: Thanks to all contributors who have helped improve this project

## Disclaimer

This application is for entertainment purposes only. The lottery is a game of chance, and past results do not predict future outcomes. Please gamble responsibly.

- **UK Gambling Help**: https://www.gambleaware.org/
- **BeGambleAware**: 0808 8020 133

---

**Note**: This is a hobby project and not affiliated with or endorsed by the National Lottery.