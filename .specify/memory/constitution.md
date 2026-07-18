# National Lottery Generator Constitution

## Project Identity

**Name**: National Lottery Generator

**Purpose**: A Laravel application that analyses UK National Lottery historical draw data and generates playful number suggestions for entertainment. This is not a forecasting tool and must never be presented as one.

**Primary language**: PHP 8.3–8.5

**Architecture**: Monolithic Laravel 13 MVC web application with server-rendered Blade views and file-based storage (no database required for core features).

**Deployment**: Azure App Service

## Core Principles

### I. Thin Controllers, Service-Layer Logic

HTTP controllers (`app/Http/Controllers/`) orchestrate requests and delegate to services. Business logic lives in `app/Services/Lottery/`. Controllers MUST NOT contain lottery analysis, CSV parsing, or number-generation algorithms.

### II. Per-Game Service Pattern

Each lottery game is implemented as a pair of service classes:

- `*Download` — fetches and caches historical draw CSV data
- `*Generate` — analyses cached data and produces suggested number lines

Shared behaviour belongs in `Downloader`, `Utils`, or `CsvDownloadService`. New games MUST follow this pattern.

### III. Config-Driven Games

Game definitions live in `config/games.php` (slug, name, logo). The `Game` model (`app/Models/Game.php`) reads from this config and is not Eloquent-backed. Adding a game requires a config entry plus corresponding service classes.

### IV. File-Based Storage (No Database for Core Features)

Historical draw data is stored on the local filesystem via Laravel's Storage facade. Cache uses file or array drivers. Features MUST NOT introduce database dependencies unless explicitly scoped and justified. SQLite is used in CI/tests only.

### V. Tests Required for Lottery Logic

New or modified lottery logic MUST include PHPUnit tests in `tests/Unit/Lottery/`. Use shared base cases (`GenerateTestCase`, `DownloaderTestCase`) where applicable. Feature tests for storage behaviour go in `tests/Feature/`.

### VI. PSR-12 Code Style (NON-NEGOTIABLE)

All PHP code MUST follow PSR-12, enforced by Laravel Pint (`pint.json`). Run `./vendor/bin/sail pint` before committing. Use type hints, return types, and PHPDoc on public classes and methods.

### VII. Entertainment-Only Disclaimer

User-facing features MUST include or preserve the entertainment-only disclaimer. Generated numbers are suggestions for fun, not predictions. Responsible gambling messaging must remain accessible.

## Code Boundaries

| Directory | Purpose | May depend on |
|-----------|---------|---------------|
| `app/Http/Controllers/` | Request handling, view data assembly | Services, Models, Views |
| `app/Services/Lottery/` | Download, analysis, generation | Laravel facades (Http, Storage, Cache, Log) |
| `app/Models/` | Domain objects (`Game` config reader, `User` scaffold) | `config/` |
| `config/` | Application and game configuration | — |
| `resources/views/games/` | Blade templates for game UI | Layout, Bootstrap assets |
| `resources/js/`, `resources/sass/` | Frontend assets (Bootstrap 5, jQuery) | Laravel Mix build |
| `routes/web.php` | Web route definitions | Controllers |
| `tests/Unit/Lottery/` | Per-game unit tests | Application services |
| `tests/Feature/` | Integration/feature tests | Application |
| `docs/` | Public user documentation | — |
| `docs-internal/` | Maintainer documentation | — |

## Naming Conventions

- **PHP classes**: PascalCase (`LottoGenerate.php`, `CsvDownloadService.php`)
- **Game slugs**: kebab-case (`set-for-life`, `lotto-hotpicks`)
- **Blade views**: kebab-case filenames in `resources/views/games/`
- **Config keys**: snake_case
- **Test files**: `*Test.php` in `tests/Unit/Lottery/` or `tests/Feature/`
- **Branches**: `feature/*` or `fix/*` for general work; Spec Kit numbered branches (`001-slug`) for SDD features

## Testing Requirements

- **Framework**: PHPUnit 12
- **Run command**: `./vendor/bin/sail artisan test` (or `vendor/bin/phpunit`)
- **Unit tests**: One test class per game service in `tests/Unit/Lottery/`
- **Feature tests**: File-based storage and end-to-end flows in `tests/Feature/`
- **CI matrix**: Tests MUST pass on PHP 8.3, 8.4, and 8.5

## Dependency Rules

- Controllers → Services → Laravel facades (allowed direction)
- Services MUST NOT depend on Controllers or Views
- Services MAY use `Downloader`, `Utils`, `CsvDownloadService` and other lottery services
- Views receive pre-formatted data from controllers; no business logic in Blade templates
- `routes/api.php` is unused; new API endpoints require explicit feature scoping

## Quality Gates

All changes MUST pass before merge:

1. **PHPUnit** — full test suite (`vendor/bin/phpunit`)
2. **Laravel Pint** — PSR-12 check (`./vendor/bin/sail pint --test`)
3. **PHPMD** — static analysis (CI scheduled scan)
4. **GitHub Actions** — Laravel workflow on push/PR to `main`

## Development Workflow

1. Create a branch (`feature/*`, `fix/*`, or Spec Kit `001-slug`)
2. Implement changes following code boundaries above
3. Add or update tests for lottery logic changes
4. Run Pint and tests locally
5. Open a pull request to `main`

Detailed contributor guidance: `CONTRIBUTING.md` and `docs-internal/development-setup.md`.

## Governance

This constitution supersedes generic Spec Kit defaults for this project. Amendments require updating this file and noting the version/date below. All feature specs, plans, and tasks MUST comply with these rules. Complexity that violates a principle MUST be documented in the plan's Complexity Tracking table with justification.

**Version**: 1.0.0 | **Ratified**: 2026-07-18 | **Last Amended**: 2026-07-18
