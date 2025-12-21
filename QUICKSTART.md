# Quick Start Guide

This is a simplified quick start guide for getting the National Lottery Generator running on your local machine in under 5 minutes.

## Prerequisites

You need:
- **Docker Desktop** installed ([Download here](https://www.docker.com/products/docker-desktop))
- **Git** installed ([Download here](https://git-scm.com/downloads))
- A terminal/command prompt

That's it! No need to install PHP, Composer, or any other dependencies.

## Installation (5 Steps)

### 1. Clone the Repository

```bash
git clone https://github.com/markheydon/national-lottery-generator.git
cd national-lottery-generator
```

### 2. Install Dependencies

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

**Windows users**: Use PowerShell and replace `$(pwd)` with `${PWD}` or the full path to your project directory.

### 3. Set Up Environment

```bash
cp .env.example .env
```

### 4. Start the Application

```bash
./vendor/bin/sail up -d
```

**Windows users**: Use `vendor/bin/sail` instead of `./vendor/bin/sail`

This will:
- Download and set up Docker containers
- Start the web server
- Configure the application

**First time?** This step takes 2-5 minutes. Subsequent starts are much faster.

### 5. Generate Application Key

```bash
./vendor/bin/sail artisan key:generate
```

## Access the Application

Open your browser and go to: **http://localhost**

🎉 **Done!** The application is now running.

## Common Commands

### Stop the Application
```bash
./vendor/bin/sail down
```

### Restart the Application
```bash
./vendor/bin/sail down
./vendor/bin/sail up -d
```

### View Logs
```bash
./vendor/bin/sail logs
```

### Run Tests
```bash
./vendor/bin/sail artisan test
```

## Troubleshooting

### "Port 80 is already in use"

Another application is using port 80 (probably Apache or another web server).

**Quick fix**: Stop the conflicting service:
```bash
# On Linux/Mac
sudo service apache2 stop
sudo service nginx stop

# On Windows
# Stop IIS or Apache from Services panel
```

### "Permission denied" errors

Fix file permissions:
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:$USER storage bootstrap/cache
```

### Nothing happens when I access localhost

1. Check if containers are running:
   ```bash
   docker ps
   ```
   You should see containers for `national-lottery-generator`

2. Check logs for errors:
   ```bash
   ./vendor/bin/sail logs
   ```

3. Try restarting:
   ```bash
   ./vendor/bin/sail down
   ./vendor/bin/sail up -d
   ```

## What's Next?

- **Read the full [README.md](README.md)** to understand how the application works
- **Check [CONTRIBUTING.md](CONTRIBUTING.md)** if you want to contribute
- **Explore the code** in the `app/` directory
- **Run the tests** to see everything working: `./vendor/bin/sail artisan test`

## Need More Help?

- Check the [Troubleshooting section](README.md#troubleshooting) in the main README
- Browse [existing issues](https://github.com/markheydon/national-lottery-generator/issues)
- Open a new issue if you're stuck

## Uninstallation

To completely remove the application:

```bash
# Stop and remove containers
./vendor/bin/sail down -v

# Navigate out of the directory
cd ..

# Remove the project directory
rm -rf national-lottery-generator
```

---

**Happy lottery number generating!** 🎲
