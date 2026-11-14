# National Lottery Generator

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.3--8.5-purple.svg)](https://www.php.net/)

This PHP application generates playful number suggestions for UK National Lottery games. It uses historical draw data, file-based caching, and a lightweight web interface to show suggested lines for Lotto, EuroMillions, Thunderball, Set For Life, Lotto Hotpicks, and EuroMillions Hotpicks.

## Requirements

- PHP 8.3 to 8.5
- Composer
- No database is required; the app uses the local filesystem for CSV caching

## Quick start

For a concise local setup guide, see [QUICKSTART.md](QUICKSTART.md).

```bash
git clone https://github.com/markheydon/national-lottery-generator.git
cd national-lottery-generator
composer install
php -S localhost:8000 -t public
```

Optional: copy `.env.example` to `.env` if you need to tune CSV download timeouts, backup retention (`LOTTERY_BACKUP_MAX`), or test URL overrides.

Then open the app at http://localhost:8000.

## Usage

The public app is a simple game selector:

1. Open the home page and choose a game card.
2. Click "Generate Numbers" to view suggested lines and the latest draw date.
3. Use the "Other Games" menu to switch to another game.

The app is designed for entertainment only. It does not predict future draws and should not be treated as a forecasting tool.

## Running the app

### Local (PHP built-in server)

```bash
composer install
php -S localhost:8000 -t public
```

Open http://localhost:8000. Full local notes: [QUICKSTART.md](QUICKSTART.md).

You can also serve `public/` with Apache (`public/.htaccess`) or nginx — see [deploy/README.md](deploy/README.md).

### Azure App Service (production)

The live site is deployed to Azure App Service on push to `main` (application CD only). Infrastructure is defined in Bicep under [deploy/bicep/](deploy/bicep/) and deployed manually when needed — see [deploy/README.md](deploy/README.md) and [docs/azure-infrastructure.md](docs/azure-infrastructure.md). Optional env: `LOTTERY_DOWNLOAD_TIMEOUT`, `LOTTERY_BACKUP_MAX`. Nginx helper: [deploy/nginx-default](deploy/nginx-default).

## Project layout

| Path | Purpose |
|------|---------|
| `public/` | Web root and front controller |
| `src/` | Application code (HTTP layer + lottery services) |
| `templates/` | Plain PHP views |
| `config/games.php` | Supported games |
| `storage/app/lottery/` | Cached draw-history CSVs |
| `tests/Unit/`, `tests/Feature/` | PHPUnit |
| `tests/e2e/` | Playwright UI tests |
| `deploy/` | Azure Bicep (`bicep/`), nginx helper, local server notes |

## Development

Detailed contributor setup lives in [docs/development-setup.md](docs/development-setup.md).

**Dev Container / Codespaces** (VS Code, Cursor, or GitHub Codespaces): open in a container and wait for `postCreate` — Composer dependencies are installed automatically. Then:

```bash
php -S 0.0.0.0:8000 -t public
vendor/bin/phpunit
```

**Local PHP** (no container):

```bash
vendor/bin/phpunit          # Unit + feature tests
vendor/bin/pint --test        # Check code style
vendor/bin/pint               # Fix code style
```

Playwright E2E specs in `tests/e2e/` run in [CI](.github/workflows/ci.yml) on every pull request (Node.js is not required in the Dev Container or for day-to-day PHP work).

CI (`.github/workflows/ci.yml`) runs Pint, PHPUnit on PHP 8.3–8.5, Playwright, and PHPMD.

## Documentation

- [Public site](https://lottery.markheydon.me.uk/) — user guides (source in `website/content/`)
- [Getting started](https://lottery.markheydon.me.uk/guides/getting-started/)
- [How the app works](https://lottery.markheydon.me.uk/guides/how-it-works/)
- [FAQ](https://lottery.markheydon.me.uk/guides/faq/)
- [Maintainer docs](docs/README.md)

## Contributing

Contributions are welcome. Please open an issue or pull request, and follow the PSR-12 conventions described in [CONTRIBUTING.md](CONTRIBUTING.md).

## License

This project is licensed under the GPL-3.0-or-later licence. See [LICENSE](LICENSE) for details.

This application is for entertainment purposes only. The lottery is a game of chance, and past results do not predict future outcomes. Please gamble responsibly.

- **UK Gambling Help**: https://www.gambleaware.org/
- **BeGambleAware**: 0808 8020 133

---

**Note**: This is a hobby project and not affiliated with or endorsed by the National Lottery.
