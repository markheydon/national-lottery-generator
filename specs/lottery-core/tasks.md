# Tasks: Lottery Core Platform

**Status**: Migrated — all tasks completed

**Input**: Reverse-engineered from existing codebase

## Phase 1: Shared Infrastructure

- [x] T001 Create `Downloader` service in `src/Services/Lottery/Downloader.php`
- [x] T002 Create `CsvDownloadService` in `src/Services/Lottery/CsvDownloadService.php`
- [x] T003 Create `Utils` helper in `src/Services/Lottery/Utils.php`
- [x] T004 [P] Unit tests for Utils in `tests/Unit/Lottery/UtilsTest.php`

## Phase 2: Game Model & Configuration

- [x] T005 Define games in `config/games.php` (6 games)
- [x] T006 Create config-driven `Game` model in `src/Game.php`
- [x] T007 Map game slugs to `Downloader` instances in `Game::getDownloader()`

## Phase 3: Web Layer

- [x] T008 Create `GameController` in `src/Http/GameController.php`
- [x] T009 Register routes in `public/index.php` (`/`, `/game/{slug}/generate`)
- [x] T010 Create game selector view `templates/games/index.php`
- [x] T011 Create generate view `templates/games/generate.php`
- [x] T012 Create layout `templates/layout.php`

## Phase 4: Tests & Quality

- [x] T013 Feature tests in `tests/Feature/FileBasedStorageTest.php`

## Gaps (Not Completed)

- [ ] T014 Feature test for full HTTP generate flow (`/game/{slug}/generate`)
- [ ] T015 Extract game dispatch from `GameController` into a dedicated service/registry
- [ ] T016 Error handling UI for failed CSV downloads
