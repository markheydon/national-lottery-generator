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
cp .env.example .env
php -S localhost:8000 -t public
```

Then open the app at http://localhost:8000.

## Usage

The public app is a simple game selector:

1. Open the home page and choose a game card.
2. Click "Generate Numbers" to view suggested lines and the latest draw date.
3. Use the "Other Games" menu to switch to another game.

The app is designed for entertainment only. It does not predict future draws and should not be treated as a forecasting tool.

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
| `deploy/nginx-default` | Azure App Service nginx helper |

## Development

Detailed contributor setup lives in [docs-internal/development-setup.md](docs-internal/development-setup.md).

```bash
vendor/bin/phpunit          # Unit + feature tests
./vendor/bin/pint --test    # Check code style
./vendor/bin/pint           # Fix code style
npx playwright test         # E2E (install @playwright/test first)
```

CI (`.github/workflows/ci.yml`) runs Pint, PHPUnit on PHP 8.3–8.5, Playwright, and PHPMD.

## Deployment

The app is deployed to Azure App Service. The deployment workflow runs `composer install --no-dev` and serves `public/` as the web root. Optional env: `LOTTERY_DOWNLOAD_TIMEOUT`.

## Documentation

- [Public docs](docs/README.md)
- [Getting started guide](docs/getting-started.md)
- [How the app works](docs/how-it-works.md)
- [FAQ](docs/faq.md)

## Contributing

Contributions are welcome. Please open an issue or pull request, and follow the PSR-12 conventions described in [CONTRIBUTING.md](CONTRIBUTING.md).

## License

This project is licensed under the GPL-3.0-or-later licence. See [LICENSE](LICENSE) for details.

This application is for entertainment purposes only. The lottery is a game of chance, and past results do not predict future outcomes. Please gamble responsibly.

- **UK Gambling Help**: https://www.gambleaware.org/
- **BeGambleAware**: 0808 8020 133

---

**Note**: This is a hobby project and not affiliated with or endorsed by the National Lottery.
