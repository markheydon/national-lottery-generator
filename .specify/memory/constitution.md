# National Lottery Generator Constitution

## Project Identity

**Name**: National Lottery Generator

**Purpose**: A PHP application that analyses UK National Lottery historical draw data and generates playful number suggestions for entertainment. This is not a forecasting tool and must never be presented as one.

**Primary language**: PHP 8.3–8.5

**Architecture**: Minimal vanilla PHP front controller with plain PHP templates and file-based storage (no database required for core features).

**Deployment**: Azure App Service

## Core Principles

### I. Thin HTTP Layer, Service-Layer Logic

The front controller and `GameController` orchestrate requests and delegate to services. Business logic lives in `src/Services/Lottery/`. The HTTP layer MUST NOT contain lottery analysis, CSV parsing, or number-generation algorithms.

### II. Per-Game Service Pattern

Each lottery game is implemented as a pair of service classes:

- `*Download` — fetches and caches historical draw CSV data
- `*Generate` — analyses cached data and produces suggested number lines

Shared behaviour belongs in `Downloader`, `Utils`, or `CsvDownloadService`. New games MUST follow this pattern.

### III. Config-Driven Games

Game definitions live in `config/games.php` (slug, name, logo). The `Game` class (`src/Game.php`) reads from this config. Adding a game requires a config entry plus corresponding service classes.

### IV. File-Based Storage (No Database for Core Features)

Historical draw data is stored on the local filesystem under `storage/app/lottery/`. Features MUST NOT introduce database dependencies unless explicitly scoped and justified.

### V. Tests Required for Lottery Logic

New or modified lottery logic MUST include PHPUnit tests in `tests/Unit/Lottery/`. Use shared base cases (`GenerateTestCase`, `DownloaderTestCase`) where applicable. Feature tests for storage and HTTP behaviour go in `tests/Feature/`. Playwright E2E specs live in `tests/e2e/`.

### VI. PSR-12 Code Style (NON-NEGOTIABLE)

All PHP code MUST follow PSR-12, enforced by Laravel Pint (`pint.json`) used as a standalone linter. Run `vendor/bin/pint` before committing. Use type hints, return types, and PHPDoc on public classes and methods.

### VII. Entertainment-Only Disclaimer

User-facing features MUST include or preserve the entertainment-only disclaimer. Generated numbers are suggestions for fun, not predictions. Responsible gambling messaging must remain accessible.

## Code Boundaries

| Directory | Purpose | May depend on |
|-----------|---------|---------------|
| `public/index.php` | Front controller / routing | HTTP layer |
| `src/Http/` | Request handling, view data assembly | Services, Game, templates |
| `src/Services/Lottery/` | Download, analysis, generation | Guzzle, filesystem helpers |
| `src/Game.php` | Config-driven game registry | `config/games.php` |
| `config/` | Game configuration | — |
| `templates/` | Plain PHP templates for game UI | Layout, public assets |
| `tests/Unit/Lottery/` | Per-game unit tests | Application services |
| `tests/Feature/` | Integration/feature tests | Application |
| `tests/e2e/` | Playwright UI tests | Running app |
| `docs/` | Public user documentation | — |
| `docs-internal/` | Maintainer documentation | — |
| `deploy/` | Azure/nginx deploy helpers | — |

## Naming Conventions

- **PHP classes**: PascalCase (`LottoGenerate.php`, `CsvDownloadService.php`)
- **Game slugs**: kebab-case (`set-for-life`, `lotto-hotpicks`)
- **Templates**: kebab-case filenames in `templates/games/`
- **Config keys**: snake_case
- **Test files**: `*Test.php` in `tests/Unit/Lottery/` or `tests/Feature/`
- **Branches**: `feature/*` or `fix/*` for general work; Spec Kit numbered branches (`001-slug`) for SDD features

## Testing Requirements

- **Framework**: PHPUnit 12
- **Run command**: `vendor/bin/phpunit`
- **Unit tests**: One test class per game service in `tests/Unit/Lottery/`
- **Feature tests**: File-based storage and HTTP flows in `tests/Feature/`
- **E2E**: Playwright specs in `tests/e2e/` (`npm run test:e2e`)

## Environment Variables

| Variable | Purpose |
|----------|---------|
| `LOTTERY_DOWNLOAD_TIMEOUT` | HTTP timeout for CSV downloads (default 30) |
| `LOTTERY_DOWNLOAD_URL_*` | Test overrides for CSV download URLs |

## Governance

This constitution supersedes conflicting guidance in outdated docs. When principles change, update this file and dependent templates/docs in the same change.
