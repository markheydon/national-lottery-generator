# Feature Specification: Lottery Core Platform

**Feature Branch**: N/A (migrated from existing code)

**Created**: 2026-07-18

**Status**: Migrated

**Input**: Reverse-engineered from existing codebase on `main` / `migrate-to-spec-kit`

## User Scenarios & Testing

### User Story 1 - Browse Available Games (Priority: P1)

A visitor opens the home page and sees a card for each configured lottery game with its logo and a link to generate numbers.

**Why this priority**: Entry point for the entire application.

**Independent Test**: Visit `/` and verify all games from `config/games.php` render as clickable cards.

**Acceptance Scenarios**:

1. **Given** the app is running, **When** a user visits `/`, **Then** they see a card for each game in `config/games.php`
2. **Given** a game card is displayed, **When** the user clicks "Generate Numbers", **Then** they are taken to `/game/{slug}/generate`

---

### User Story 2 - Generate Numbers for a Game (Priority: P1)

A visitor selects a game and views suggested number lines based on historical draw data, with the latest draw date shown.

**Why this priority**: Core value proposition of the application.

**Independent Test**: Visit `/game/lotto/generate` and verify suggested lines, other suggestions, latest draw date, and disclaimer are displayed.

**Acceptance Scenarios**:

1. **Given** a valid game slug, **When** a user visits `/game/{slug}/generate`, **Then** suggested number lines are displayed in a formatted table
2. **Given** historical CSV data is stale or missing, **When** generation is requested, **Then** the system downloads fresh data before generating
3. **Given** an invalid slug, **When** a user visits `/game/invalid/generate`, **Then** a 404 response is returned

---

### User Story 3 - Switch Between Games (Priority: P2)

While viewing generated numbers, a user can navigate to another game via the "Other Games" dropdown without returning to the home page.

**Independent Test**: On any generate page, use the dropdown to navigate to a different game.

**Acceptance Scenarios**:

1. **Given** a user is on a generate page, **When** they open "Other Games", **Then** all other configured games are listed
2. **Given** the dropdown is open, **When** they select a game, **Then** they navigate to that game's generate page

---

### Edge Cases

- What happens when CSV download fails (network error)?
- What happens when `config/games.php` has no games defined?
- How does the system handle a game slug with no matching generate service in `GameController`?

## Requirements

### Functional Requirements

- **FR-001**: System MUST load game definitions from `config/games.php`
- **FR-002**: System MUST serve game selection at `/` via `GameController@index`
- **FR-003**: System MUST serve number generation at `/game/{slug}/generate` via `GameController@generate`
- **FR-004**: System MUST check CSV freshness via `CsvDownloadService::isDownloadRequired()` before generating
- **FR-005**: System MUST download historical draw CSVs via `Downloader` when data is missing or older than 24 hours
- **FR-006**: System MUST format generated lines for display (zero-padded numbers, separators, bonus ball markers)
- **FR-007**: System MUST display the latest draw date from the historical data used
- **FR-008**: System MUST show an entertainment-only disclaimer on generate pages

### Key Entities

- **Game**: Config-driven object with slug, name, logo; provides a `Downloader` instance per game slug
- **Downloader**: Shared CSV download/cache service using Laravel Storage
- **CsvDownloadService**: Freshness check for cached CSV files

## Success Criteria

- **SC-001**: All 6 configured games are accessible from the home page
- **SC-002**: Generate pages display within 3 seconds under normal conditions
- **SC-003**: `FileBasedStorageTest` passes — games load from config, downloaders instantiate, storage is accessible
- **SC-004**: Entertainment disclaimer is visible on every generate page

## Assumptions

- National Lottery CSV API endpoints remain available
- File-based storage (`storage/app/lottery/`) is writable
- No database is required for core functionality
- Game logos exist in `public/img/`

## Gaps Identified

- ⚠️ `GameController` contains a large switch statement for game dispatch (constitution recommends thin controllers)
- ⚠️ No feature/integration test for the full HTTP generate flow
- ⚠️ No explicit error UI when CSV download fails during generation
