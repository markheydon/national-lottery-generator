# Implementation Plan: Lottery Core Platform

**Branch**: N/A (migrated) | **Date**: 2026-07-18 | **Spec**: [spec.md](./spec.md)

**Status**: Migrated — feature already implemented

## Summary

Core platform providing game configuration, CSV download infrastructure, web routes, game selection UI, and number generation orchestration. Serves as the foundation that per-game services plug into.

## Technical Context

**Language/Version**: PHP 8.3–8.5, Laravel 13

**Primary Dependencies**: Laravel HTTP/Storage/Cache facades, Guzzle (via Laravel HTTP)

**Storage**: Local filesystem via Laravel Storage (`storage/app/lottery/`)

**Testing**: PHPUnit 12 — `tests/Feature/FileBasedStorageTest.php`, `tests/Unit/Lottery/UtilsTest.php`

**Project Type**: Monolithic Laravel MVC

## Constitution Check

- [x] Business logic delegated to `app/Services/Lottery/` (partial — dispatch logic remains in controller)
- [x] File-based storage, no database for core features
- [x] Entertainment disclaimer on generate pages
- [ ] Thin controllers — `GameController` is 304 lines with inline dispatch (violation noted)

## Project Structure

```text
app/
├── Http/Controllers/GameController.php   # index + generate orchestration
├── Models/Game.php                        # Config-driven game model
└── Services/Lottery/
    ├── Downloader.php                     # Shared CSV download
    ├── CsvDownloadService.php             # Freshness checks
    └── Utils.php                          # CSV parsing, frequency analysis

config/games.php                           # 6 game definitions

resources/views/
├── layout.blade.php
└── games/
    ├── index.blade.php                    # Game selector
    └── generate.blade.php                 # Number display + disclaimer

routes/web.php                             # 2 routes

tests/
├── Feature/FileBasedStorageTest.php
└── Unit/Lottery/UtilsTest.php
```

## Implementation Phases (Completed)

### Phase A: Shared Infrastructure — DONE

`Downloader`, `CsvDownloadService`, `Utils` provide CSV download, caching, parsing, and statistical helpers.

### Phase B: Game Model & Configuration — DONE

`config/games.php` defines 6 games. `Game` model reads config, caches instances, and maps slugs to `Downloader` instances.

### Phase C: Web Layer — DONE

`routes/web.php` defines 2 routes. `GameController` handles index and generate. Blade views render game cards and formatted number tables.

### Phase D: Tests — DONE (partial)

`FileBasedStorageTest` covers config loading and storage. `UtilsTest` covers utility methods. No full HTTP feature test for generate flow.

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| Game dispatch switch in controller | Historical implementation | Per-game services exist but orchestration was never extracted |
