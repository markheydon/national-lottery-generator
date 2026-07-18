# Implementation Plan: [FEATURE]

**Branch**: `[###-feature-name]` | **Date**: [DATE] | **Spec**: [link]

**Input**: Feature specification from `/specs/[###-feature-name]/spec.md`

**Note**: This template is filled in by the `/speckit-plan` command; its definition describes the execution workflow.

## Summary

[Extract from feature spec: primary requirement + technical approach from research]

## Technical Context

**Language/Version**: PHP 8.3–8.5 (Laravel 13 supported range)

**Primary Dependencies**: Laravel 13, Guzzle (HTTP), Laravel Mix 6 (Webpack), Bootstrap 5, jQuery

**Storage**: File-based cache and local filesystem via Laravel Storage facade (no database for core features)

**Testing**: PHPUnit 12 (`./vendor/bin/sail artisan test`)

**Target Platform**: Linux (Laravel Sail / Azure App Service)

**Project Type**: Monolithic Laravel MVC web application with server-rendered Blade views

**Performance Goals**: Page load and number generation under 3 seconds for typical draw history files

**Constraints**: No database dependency for core features; entertainment-only purpose; PSR-12 code style

**Scale/Scope**: Single-server hobby project; 6 existing lottery games

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- [ ] Business logic stays in `app/Services/Lottery/`, not controllers
- [ ] New games follow `*Download` + `*Generate` service pattern
- [ ] Game config added to `config/games.php` if applicable
- [ ] PHPUnit tests planned for lottery logic changes
- [ ] No new database dependencies without justification
- [ ] Entertainment-only disclaimer preserved in user-facing output
- [ ] PSR-12 / Pint compliance expected

## Project Structure

### Documentation (this feature)

```text
specs/[###-feature]/
├── plan.md              # This file (/speckit-plan command output)
├── research.md          # Phase 0 output (/speckit-plan command)
├── data-model.md        # Phase 1 output (/speckit-plan command)
├── quickstart.md        # Phase 1 output (/speckit-plan command)
├── contracts/           # Phase 1 output (/speckit-plan command)
└── tasks.md             # Phase 2 output (/speckit-tasks command - NOT created by /speckit-plan)
```

### Source Code (repository root)

```text
app/
├── Http/
│   └── Controllers/
│       └── GameController.php       # Web route handlers
├── Models/
│   └── Game.php                     # Config-driven game model
└── Services/
    └── Lottery/
        ├── Downloader.php           # Shared CSV download/caching
        ├── CsvDownloadService.php   # Download freshness checks
        ├── Utils.php                # Shared lottery utilities
        ├── {Game}Download.php       # Per-game download service
        └── {Game}Generate.php       # Per-game generation service

config/
└── games.php                        # Game definitions (slug, name, logo)

resources/
├── views/
│   └── games/
│       ├── index.blade.php          # Game selector
│       └── generate.blade.php       # Number display
├── js/app.js                        # Frontend JS (minimal)
└── sass/app.scss                    # Bootstrap styles

routes/
└── web.php                          # Web routes

tests/
├── Unit/
│   └── Lottery/
│       ├── GenerateTestCase.php     # Shared generate test base
│       ├── DownloaderTestCase.php   # Shared download test base
│       ├── {Game}GenerateTest.php   # Per-game generate tests
│       └── {Game}DownloadTest.php   # Per-game download tests
└── Feature/
    └── FileBasedStorageTest.php     # Storage integration tests
```

**Structure Decision**: Single Laravel monolith. All feature code lives under `app/`, `config/`, `resources/`, and `routes/` at the repository root. No separate frontend/backend packages.

## Implementation Phases

### Phase A: Lottery Services

Implement or modify `*Download` and `*Generate` classes in `app/Services/Lottery/`. Shared logic goes in `Downloader`, `Utils`, or `CsvDownloadService`.

### Phase B: Configuration & Routing

Add game entries to `config/games.php`. Register routes in `routes/web.php`. Wire game dispatch in `GameController`.

### Phase C: Views & Assets

Create or update Blade templates in `resources/views/games/`. Compile assets with `npm run dev` or `npm run prod` if SCSS/JS changes are needed.

### Phase D: Tests & Quality

Add PHPUnit tests in `tests/Unit/Lottery/`. Run `./vendor/bin/sail artisan test` and `./vendor/bin/sail pint --test`.

## Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| [e.g., database dependency] | [current need] | [why file storage insufficient] |
