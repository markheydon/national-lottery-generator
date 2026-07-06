# National Lottery Generator

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](LICENSE)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-red.svg)](https://laravel.com/docs/13.x)
[![PHP](https://img.shields.io/badge/PHP-8.3--8.5-purple.svg)](https://www.php.net/)

This Laravel 13 application generates playful number suggestions for UK National Lottery games. It uses historical draw data, file-based caching, and a lightweight web interface to show suggested lines for Lotto, EuroMillions, Thunderball, Set For Life, Lotto Hotpicks, and EuroMillions Hotpicks.

## Requirements

- PHP 8.3 to 8.5
- Composer
- Docker and Laravel Sail are optional, but recommended for local development
- No database is required; the app uses file cache and local filesystem storage

## Quick start

For a concise local setup guide, see [QUICKSTART.md](QUICKSTART.md).

```bash
git clone https://github.com/markheydon/national-lottery-generator.git
cd national-lottery-generator
composer install
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
```

Then open the app at http://localhost.

## Usage

The public app is a simple game selector:

1. Open the home page and choose a game card.
2. Click "Generate Numbers" to view suggested lines and the latest draw date.
3. Use the "Other Games" menu to switch to another game.

The app is designed for entertainment only. It does not predict future draws and should not be treated as a forecasting tool.

## Development

The main README stays focused on the project overview and quick start. Detailed contributor setup and maintenance guidance now lives in [docs-internal/development-setup.md](docs-internal/development-setup.md).

The GitHub Actions workflows run the test suite for PHP 8.3, 8.4, and 8.5.

## Deployment

The app is deployed to Azure App Services and uses file-based storage. The deployment workflow expects the usual Laravel environment variables, including `APP_KEY`, `CACHE_DRIVER=file`, and `FILESYSTEM_DISK=local`.

## Documentation

- [Public docs](docs/README.md)
- [Getting started guide](docs/getting-started.md)
- [How the app works](docs/how-it-works.md)
- [FAQ](docs/faq.md)

## Contributing

Contributions are welcome. Please open an issue or pull request, and follow the existing Laravel and Pint conventions described in [CONTRIBUTING.md](CONTRIBUTING.md).

## License

This project is licensed under the GPL-3.0-or-later licence. See [LICENSE](LICENSE) for details.

This application is for entertainment purposes only. The lottery is a game of chance, and past results do not predict future outcomes. Please gamble responsibly.

- **UK Gambling Help**: https://www.gambleaware.org/
- **BeGambleAware**: 0808 8020 133

---

**Note**: This is a hobby project and not affiliated with or endorsed by the National Lottery.