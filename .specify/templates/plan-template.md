# Implementation Plan: [FEATURE]

**Branch**: `[###-feature-name]` | **Date**: [DATE] | **Spec**: [link]

**Input**: Feature specification from `/specs/[###-feature-name]/spec.md`

**Note**: This template is filled in by the `/speckit-plan` command; its definition describes the execution workflow.

## Summary

[Extract from feature spec: primary requirement + technical approach from research]

## Technical Context

**Language/Version**: PHP 8.3–8.5

**Primary Dependencies**: Guzzle (HTTP); committed Bootstrap/jQuery assets under `public/`

**Storage**: Local filesystem under `storage/app/lottery/` (no database for core features)

**Testing**: PHPUnit 12 (`vendor/bin/phpunit`)

**Target Platform**: Linux (PHP built-in server / Azure App Service)

**Project Type**: Minimal vanilla PHP front controller with plain PHP templates

**Performance Goals**: Page load and number generation under 3 seconds for typical draw history files

**Constraints**: No database dependency for core features; entertainment-only purpose; PSR-12 code style

**Scale/Scope**: Single-server hobby project; 6 existing lottery games

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- [ ] Business logic stays in `src/Services/Lottery/`, not controllers
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
src/
├── Http/
│   ├── Application.php              # Front-controller routing
│   ├── GameController.php           # Presentation + dispatch
│   ├── Response.php
│   └── View.php
├── Game.php                         # Config-driven game registry
└── Services/
    └── Lottery/
        ├── Downloader.php           # Shared CSV download/caching
        ├── CsvDownloadService.php   # Download freshness checks
        ├── Utils.php                # Shared lottery utilities
        ├── {Game}Download.php       # Per-game download service
        └── {Game}Generate.php       # Per-game generation service

config/
└── games.php                        # Game definitions (slug, name, logo)

templates/
├── layout.php
└── games/
    ├── index.php                    # Game selector
    └── generate.php                 # Number display

public/
└── index.php                        # Front controller

tests/
├── Unit/
│   └── Lottery/
│       ├── GenerateTestCase.php
│       ├── DownloaderTestCase.php
│       ├── {Game}GenerateTest.php
│       └── {Game}DownloadTest.php
├── Feature/
│   └── FileBasedStorageTest.php
└── e2e/                             # Playwright UI tests
```

**Structure Decision**: Vanilla PHP app. Feature code lives under `src/`, `config/`, `templates/`, and `public/` at the repository root.

## Implementation Phases

### Phase A: Lottery Services

Implement or modify `*Download` and `*Generate` classes in `src/Services/Lottery/`. Shared logic goes in `Downloader`, `Utils`, or `CsvDownloadService`.

### Phase B: Configuration & Routing

Add game entries to `config/games.php`. Wire routes in `public/index.php` / `Application` and dispatch in `GameController`.

### Phase C: Views & Assets

Create or update PHP templates in `templates/games/`. Do not rebuild frontend assets unless the Mix/webpack issue is separately resolved.

### Phase D: Tests & Quality

Add PHPUnit tests in `tests/Unit/Lottery/`. Run `vendor/bin/phpunit` and `./vendor/bin/pint --test`.

## Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| [e.g., database dependency] | [current need] | [why file storage insufficient] |
