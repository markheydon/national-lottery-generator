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

Optional: copy `.env.example` to `.env` if you need to tune CSV download timeouts or test URL overrides.

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

The live site is deployed to Azure App Service. The workflow runs `composer install --no-dev` and uses `public/` as the web root. Optional env: `LOTTERY_DOWNLOAD_TIMEOUT`. Nginx helper: [deploy/nginx-default](deploy/nginx-default).

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
| `deploy/` | Optional nginx / Azure helpers (local or cloud) |

## Development

Detailed contributor setup lives in [docs-internal/development-setup.md](docs-internal/development-setup.md).

**Dev Container / Codespaces** (VS Code, Cursor, or GitHub Codespaces): open in a container and wait for `postCreate` — Composer, npm, and Playwright Chromium are installed automatically. Then:

```bash
php -S 0.0.0.0:8000 -t public
vendor/bin/phpunit
npm run test:e2e
```

**Local PHP** (no container):

```bash
vendor/bin/phpunit          # Unit + feature tests
vendor/bin/pint --test        # Check code style
vendor/bin/pint               # Fix code style
npm ci && npm run test:e2e:install && npm run test:e2e   # E2E (requires Node.js)
```

On a Windows drive mount in a Dev Container, if `npm ci` fails with `EPERM` / `chmod`, rebuild the Dev Container (it uses Docker volumes for Composer and `node_modules/`), or see [QUICKSTART.md](QUICKSTART.md#npm-install-fails-with-eperm-dev-container-on-windows).

CI (`.github/workflows/ci.yml`) runs Pint, PHPUnit on PHP 8.3–8.5, Playwright, and PHPMD.

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
