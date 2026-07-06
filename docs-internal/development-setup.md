# Development Setup and Workflow

This page collects the practical setup and maintenance guidance that is useful for contributors and maintainers.

## Codespaces

The repository includes a devcontainer configuration, so you can open it in GitHub Codespaces and use the preconfigured PHP, Composer, Node.js, and Docker environment without installing those tools locally.

After the container finishes building, the usual first-time setup is:

```bash
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
```

## Local development with Laravel Sail

If you do not already have PHP and Composer installed on the host machine, the most reliable first-time bootstrap is to use Docker to install dependencies before starting Sail.

```bash
docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$(pwd):/var/www/html" \
  -w /var/www/html \
  laravelsail/php83-composer:latest \
  composer install --ignore-platform-reqs

cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
```

## Daily workflow

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan test
./vendor/bin/sail pint --test
./vendor/bin/sail down
```

## Code style

The project follows PSR-12 through Laravel Pint:

```bash
./vendor/bin/sail pint --test
./vendor/bin/sail pint
```

## Common issues

### Port 80 already in use

If you see a port conflict when starting Sail, stop the conflicting service or change the exposed port in the Docker configuration before restarting Sail.

### Storage permissions

If the application cannot write to storage or cache directories, fix the permissions before retrying:

```bash
sudo chmod -R 775 storage
sudo chown -R "$USER:$USER" storage
```

### Tests failing after a fresh install

Clear the relevant caches and rerun the test suite:

```bash
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan test
```

### CSV download timeout

If lottery data downloads are timing out, increase the timeout in the environment file and restart Sail:

```bash
LOTTERY_DOWNLOAD_TIMEOUT=60
```
