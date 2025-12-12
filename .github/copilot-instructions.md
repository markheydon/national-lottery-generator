# Copilot Instructions for National Lottery Generator

## Project Overview

This is a Laravel 11 application that generates National Lottery numbers using a custom algorithm. The application is deployed on Azure App Services and uses MySQL for data storage.

## Technology Stack

- **Framework**: Laravel 11.x (LTS)
- **PHP Version**: 8.2 or higher
- **Database**: MySQL (Azure MySQL in production)
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
6. Run migrations: `./vendor/bin/sail artisan migrate:fresh --seed`

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
- Follow Laravel testing conventions (RefreshDatabase, etc.)

## Database

### Local Development

- Uses MySQL via Laravel Sail
- Database seeding is configured (see `database/seeders/`)
- Run migrations: `./vendor/bin/sail artisan migrate`
- Fresh start: `./vendor/bin/sail artisan migrate:fresh --seed`

### Azure Production

- Uses Azure MySQL with SSL connection
- Custom connection: `azure-mysql` (see `config/database.php`)
- SSL certificate location: `/home/site/wwwroot/ssl/DigiCertGlobalRootCA.crt.pem`
- Environment variables: `DB_CONNECTION=azure-mysql`, `MYSQL_ATTR_SSL_CA`

## Deployment

### Azure App Services

- Application is deployed to Azure App Services
- Nginx configuration: Custom `nginx-default` file in root
- Startup command copies nginx config and reloads service
- SSL certificates in `ssl/` directory for Azure MySQL
- Post-deployment: Run `php artisan migrate:fresh --seed` from SSH

### Environment Configuration

- Set `APP_KEY` in Azure Configuration->Application settings
- Database connection uses `azure-mysql` configuration
- All sensitive values should be in environment variables, not code

## Common Tasks

### Adding New Features

1. Create/modify models in `app/Models/`
2. Create controllers in `app/Http/Controllers/`
3. Add routes in `routes/web.php` or `routes/api.php`
4. Create views in `resources/views/`
5. Add tests in `tests/Feature/` or `tests/Unit/`
6. Run `./vendor/bin/sail pint` before committing

### Database Changes

1. Create migration: `./vendor/bin/sail artisan make:migration`
2. Modify migration file in `database/migrations/`
3. Run migration: `./vendor/bin/sail artisan migrate`
4. Update seeders if needed in `database/seeders/`

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
- **Use migrations** for all database schema changes
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

# Run migrations
./vendor/bin/sail artisan migrate

# Fresh database with seeding
./vendor/bin/sail artisan migrate:fresh --seed

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

- Review the main [README.md](../README.md) for setup instructions
- Check Laravel 11 documentation: https://laravel.com/docs/11.x
- Review existing code for patterns and conventions
- Ask questions if requirements are unclear
