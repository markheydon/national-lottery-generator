# Copilot Instructions for National Lottery Generator

## Project Overview

This is a Laravel 11 application that generates National Lottery numbers using a custom algorithm. The application is deployed on Azure App Services and uses file-based storage (no database required).

## Technology Stack

- **Framework**: Laravel 11.x (LTS)
- **PHP Version**: 8.2 or higher
- **Storage**: File cache and filesystem storage (no database required)
- **Development Environment**: Laravel Sail (Docker-based)
- **Frontend**: Blade templates with webpack mix
- **Package Manager**: Composer (PHP), npm/yarn (JavaScript)

## Coding Standards

### PHP Code Style

- Follow **PSR-12** coding standards strictly
- Use **Laravel Pint** for automated code formatting
- Run `./vendor/bin/sail pint --test` to check code style
- Run `./vendor/bin/sail pint` to automatically fix issues
- Configuration is in `pint.json` (preset: psr12)

### General Conventions

- Follow Laravel best practices and conventions
- Use meaningful variable and function names
- Write clear, self-documenting code
- Keep controllers thin, use services for business logic
- Follow RESTful principles for API endpoints
- Use Laravel's built-in features (validation, eloquent, etc.)

## Project Structure

```
app/
├── Console/         # Artisan commands
├── Exceptions/      # Custom exception handlers
├── Http/           # Controllers, middleware, requests
├── Models/         # Eloquent models
├── Providers/      # Service providers
└── Services/       # Business logic services

config/             # Configuration files
database/           # Migrations, seeders, factories
resources/          # Views, assets
routes/             # Route definitions (web.php, api.php)
tests/              # PHPUnit tests
  ├── Feature/      # Feature/integration tests
  └── Unit/         # Unit tests
```

## Development Workflow

### Setting Up Local Environment

1. Use Laravel Sail for local development (Docker-based)
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env`
4. Start Sail: `./vendor/bin/sail up -d`
5. Generate app key: `./vendor/bin/sail artisan key:generate`
6. Access the application at http://localhost

### Before Making Changes

1. Always run tests before starting: `./vendor/bin/sail artisan test`
2. Check existing code style: `./vendor/bin/sail pint --test`
3. Ensure Sail is running: `./vendor/bin/sail up -d`

### After Making Changes

1. **Lint**: Run `./vendor/bin/sail pint` to fix code style
2. **Test**: Run `./vendor/bin/sail artisan test` to verify changes
3. **Manual Testing**: Access application at http://localhost
4. Commit changes with clear, descriptive messages

## Testing

- Use PHPUnit for testing (configured in `phpunit.xml`)
- Write feature tests for user-facing functionality
- Write unit tests for services and business logic
- Run tests: `./vendor/bin/sail artisan test`
- Test files should mirror the app structure
- Follow Laravel testing conventions

## Storage

### File-Based Storage

The application uses Laravel's file cache and filesystem storage (no database required):

- **CSV Downloads**: Lottery draw history CSV files are downloaded from the National Lottery website and stored in `storage/app/lottery/`
- **Caching**: Parsed draw data is cached using Laravel's file cache driver to avoid re-downloading and re-parsing between requests
- **Auto-Refresh**: Downloads are automatically refreshed when files are older than 1 day
- **Storage Locations**:
  - CSV files: `storage/app/lottery/*.csv`
  - Parsed JSON: `storage/app/lottery/*.json` (optional, for debugging)
  - Cache data: `storage/framework/cache/data/` (file cache driver)

### Configuration

- Cache driver: `CACHE_DRIVER=file`
- Filesystem disk: `FILESYSTEM_DISK=local`
- Download timeout: `LOTTERY_DOWNLOAD_TIMEOUT=30` (timeout in seconds for CSV downloads, defaults to 30)

## Deployment

### Azure App Services

- Application is deployed to Azure App Services
- Nginx configuration: Custom `nginx-default` file in root
- Startup command copies nginx config and reloads service
- Ensure write permissions for `storage/` directory

### Environment Configuration

- Set `APP_KEY` in Azure Configuration->Application settings
- Set `CACHE_DRIVER=file` for file-based caching
- Set `FILESYSTEM_DISK=local` for local filesystem storage
- All sensitive values should be in environment variables, not code

## Common Tasks

### Adding New Features

1. Create/modify models in `app/Models/`
2. Create controllers in `app/Http/Controllers/`
3. Add routes in `routes/web.php` or `routes/api.php`
4. Create views in `resources/views/`
5. Add tests in `tests/Feature/` or `tests/Unit/`
6. Run `./vendor/bin/sail pint` before committing

### Configuration Changes

1. Game configurations are stored in `config/games.php`
2. Modify the configuration file to add or update lottery games
3. No database migrations are needed as the app uses file-based storage

### Adding Dependencies

- PHP packages: `./vendor/bin/sail composer require <package>`
- JavaScript packages: `./vendor/bin/sail npm install <package>`
- Always commit `composer.lock` and `package-lock.json`/`yarn.lock`

## Important Notes

- **Never commit** `.env` file or secrets
- **Always use** Laravel Sail commands (prefixed with `./vendor/bin/sail`)
- **Test thoroughly** before requesting reviews
- **Follow PSR-12** - run Pint before committing
- **Write tests** for new functionality
- **Document** complex logic with clear comments when necessary
- **Update config files** for game configurations (see `config/games.php`)
- **Respect existing** architectural patterns and conventions

## Useful Commands

```bash
# Start development environment
./vendor/bin/sail up -d

# Run tests
./vendor/bin/sail artisan test

# Check code style
./vendor/bin/sail pint --test

# Fix code style
./vendor/bin/sail pint

# Clear cache
./vendor/bin/sail artisan cache:clear

# Stop environment
./vendor/bin/sail down

# Access Laravel Tinker
./vendor/bin/sail artisan tinker

# Clear cache
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan view:clear
```

## Getting Help

- Review the main README.md in the repository root for setup instructions
- Check Laravel 11 documentation: https://laravel.com/docs/11.x
- Review existing code for patterns and conventions
- Ask questions if requirements are unclear
