# Implementation Plan: Lottery Core Platform

**Branch**: N/A (migrated) | **Date**: 2026-07-18 | **Spec**: [spec.md](./spec.md)

**Status**: Migrated — feature already implemented

## Summary

Core platform providing game configuration, CSV download infrastructure, web routes, game selection UI, and number generation orchestration. Serves as the foundation that per-game services plug into.

## Technical Context

**Language/Version**: PHP 8.3–8.5, vanilla PHP (no framework)

**Primary Dependencies**: Guzzle HTTP client, vlucas/phpdotenv

**Storage**: Local filesystem (`storage/app/lottery/`)

**Testing**: PHPUnit 12 — `tests/Feature/FileBasedStorageTest.php`, `tests/Unit/Lottery/UtilsTest.php`

**Project Type**: Minimal PHP front controller with plain PHP templates

## Constitution Check

- [x] Business logic delegated to `src/Services/Lottery/` including dispatch via `GameGenerateRegistry`
- [x] File-based storage, no database for core features
- [x] Entertainment disclaimer on generate pages
- [x] Thin controllers — `GameController` handles HTTP orchestration and view formatting only

## Project Structure

```text
src/
├── Http/GameController.php                # index + generate HTTP orchestration
├── Http/Application.php                   # front-controller routing
├── Game.php                               # Config-driven game model
└── Services/Lottery/
    ├── Downloader.php                     # Shared CSV download
    ├── CsvDownloadService.php             # Freshness checks
    ├── GameGenerateRegistry.php           # Slug → download/read/generate dispatch
    ├── DrawHistoryService.php             # CSV availability before generate
    └── Utils.php                          # CSV parsing, frequency analysis

config/games.php                           # 6 game definitions

templates/
├── layout.php
└── games/
    ├── index.php                          # Game selector
    └── generate.php                       # Number display + disclaimer

public/index.php                           # 2 routes

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

`public/index.php` defines 2 routes. `GameController` handles index and generate. PHP templates render game cards and formatted number tables.

### Phase D: Tests — DONE

`FileBasedStorageTest` covers HTTP routes and storage. `GameGenerateRegistryTest` and per-game unit tests cover dispatch and generation.

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| _(none)_ | — | Game dispatch extracted to `GameGenerateRegistry` |
