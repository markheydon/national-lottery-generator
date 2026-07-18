# AGENTS.md

Agent orientation for the National Lottery Generator. This Laravel 13 application analyses UK National Lottery historical draw data and generates playful number suggestions for entertainment. It is not a forecasting tool.

## Architecture

Monolithic Laravel MVC with server-rendered Blade views and file-based storage (no database for core features).

| Area | Path |
|------|------|
| Lottery services | `app/Services/Lottery/` — per-game `*Download` and `*Generate` classes |
| HTTP layer | `app/Http/Controllers/GameController.php` |
| Game config | `config/games.php` — slug, name, logo per game |
| Views | `resources/views/games/` |
| Routes | `routes/web.php` — `/` and `/game/{slug}/generate` |
| Unit tests | `tests/Unit/Lottery/` |
| Feature tests | `tests/Feature/` |

Hotpicks variants (`lotto-hotpicks`, `euromillions-hotpicks`) share their parent game's download service. See `app/Models/Game.php::getDownloader()`.

Draw history CSVs are stored in `storage/app/lottery/`. Downloads refresh when files are older than 24 hours (`CsvDownloadService`). Optional env: `LOTTERY_DOWNLOAD_TIMEOUT` (default 30s).

## Essential Commands

```bash
./vendor/bin/sail up -d              # Start dev environment
./vendor/bin/sail artisan test       # Run PHPUnit
./vendor/bin/sail pint --test        # Check PSR-12
./vendor/bin/sail pint               # Fix code style
npm run dev                          # Compile assets (yarn also works)
npm run prod                         # Production assets
```

## Adding a New Game

1. Add entry to `config/games.php` (slug, name, logo)
2. Create `app/Services/Lottery/{Game}Download.php` and `{Game}Generate.php`
3. Add unit tests in `tests/Unit/Lottery/`
4. Wire dispatch in `GameController` (refactor tracked in #189)

## Constraints

- Entertainment-only disclaimer must remain on user-facing pages
- No database dependency for core features without explicit scoping
- PSR-12 via Laravel Pint — run before committing
- PHPUnit tests required for new or modified lottery logic
- Business logic belongs in `app/Services/Lottery/`, not controllers

## Spec Kit / SDD

This project uses [Spec Kit](https://github.com/github/spec-kit) for spec-driven development.

| Resource | Purpose |
|----------|---------|
| `.specify/memory/constitution.md` | Binding project principles |
| `specs/` | Feature specs, plans, and tasks |
| `.cursor/skills/speckit-*` | Cursor slash commands for SDD workflow |

### Feature specs (migrated)

| Feature | Path |
|---------|------|
| Core platform | `specs/lottery-core/` |
| Lotto | `specs/lotto/` |
| EuroMillions | `specs/euromillions/` |
| Thunderball | `specs/thunderball/` |
| Set For Life | `specs/set-for-life/` |
| Lotto Hotpicks | `specs/lotto-hotpicks/` |
| EuroMillions Hotpicks | `specs/euromillions-hotpicks/` |
| Auth scaffold (dormant) | `specs/auth-scaffold/` |

New features: use `/speckit-specify` on a Spec Kit numbered branch (`001-slug`).

## Maintaining Documentation

Use `.agents/skills/repo-update-docs/SKILL.md` to refresh `README.md` and public `docs/`. Do not edit `docs-internal/` unless asked.

## Pointers

- Human contributors: `CONTRIBUTING.md`
- Maintainer setup: `docs-internal/development-setup.md`
- Supported PHP versions: `docs-internal/supported-versions.md`
- Public user docs: `docs/`
- Open follow-ups: #187 (Set For Life tests), #188 (constitution hotpicks note), #189 (GameController refactor), #190 (HTTP feature test)
