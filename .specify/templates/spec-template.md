# Feature Specification: [FEATURE NAME]

**Feature Branch**: `[###-feature-name]`

**Created**: [DATE]

**Status**: Draft

**Input**: User description: "$ARGUMENTS"

## User Scenarios & Testing *(mandatory)*

<!--
  IMPORTANT: User stories should be PRIORITIZED as user journeys ordered by importance.
  Each user story/journey must be INDEPENDENTLY TESTABLE - meaning if you implement just ONE of them,
  you should still have a viable MVP (Minimum Viable Product) that delivers value.

  Assign priorities (P1, P2, P3, etc.) to each story, where P1 is the most critical.
  Think of each story as a standalone slice of functionality that can be:
  - Developed independently
  - Tested independently
  - Deployed independently
  - Demonstrated to users independently
-->

### User Story 1 - [Brief Title] (Priority: P1)

[Describe this user journey in plain language]

**Why this priority**: [Explain the value and why it has this priority level]

**Independent Test**: [Describe how this can be tested independently - e.g., "Can be fully tested by [specific action] and delivers [specific value]"]

**Acceptance Scenarios**:

1. **Given** [initial state], **When** [action], **Then** [expected outcome]
2. **Given** [initial state], **When** [action], **Then** [expected outcome]

---

### User Story 2 - [Brief Title] (Priority: P2)

[Describe this user journey in plain language]

**Why this priority**: [Explain the value and why it has this priority level]

**Independent Test**: [Describe how this can be tested independently]

**Acceptance Scenarios**:

1. **Given** [initial state], **When** [action], **Then** [expected outcome]

---

### User Story 3 - [Brief Title] (Priority: P3)

[Describe this user journey in plain language]

**Why this priority**: [Explain the value and why it has this priority level]

**Independent Test**: [Describe how this can be tested independently]

**Acceptance Scenarios**:

1. **Given** [initial state], **When** [action], **Then** [expected outcome]

---

[Add more user stories as needed, each with an assigned priority]

### Edge Cases

- What happens when historical CSV data is unavailable or stale?
- What happens when a game slug in the URL does not match any entry in `config/games.php`?
- How does the system handle download failures from external lottery data sources?
- What happens when generated lines contain duplicate numbers within a line?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST [specific capability]
- **FR-002**: System MUST [specific capability]
- **FR-003**: Users MUST be able to [key interaction]
- **FR-004**: System MUST [data requirement]
- **FR-005**: System MUST [behaviour]

*Example of marking unclear requirements:*

- **FR-006**: System MUST [behaviour] via [NEEDS CLARIFICATION: approach not specified]

### Game Configuration *(include if feature adds or modifies a lottery game)*

- **GC-001**: Game MUST be registered in `config/games.php` with `slug`, `name`, and `logo`
- **GC-002**: Game slug MUST be kebab-case and match the URL pattern `/game/{slug}/generate`
- **GC-003**: Game logo file MUST exist in the public assets directory

### Lottery Service Requirements *(include if feature involves draw data or number generation)*

- **LS-001**: Download logic MUST be implemented in `app/Services/Lottery/{Game}Download.php`
- **LS-002**: Generation logic MUST be implemented in `app/Services/Lottery/{Game}Generate.php`
- **LS-003**: Services MUST use `Downloader` and/or `CsvDownloadService` for CSV caching
- **LS-004**: Generated output MUST include `gameName`, `latestDrawDate`, and `lines` array
- **LS-005**: Unit tests MUST be added in `tests/Unit/Lottery/{Game}GenerateTest.php` and/or `{Game}DownloadTest.php`

### Web / Blade UI Requirements *(include if feature has user-facing pages)*

- **UI-001**: Pages MUST extend the existing layout (`resources/views/layout.blade.php`)
- **UI-002**: Game pages MUST live in `resources/views/games/`
- **UI-003**: Controller MUST pass pre-formatted data to views (no business logic in Blade)
- **UI-004**: UI MUST use existing Bootstrap 5 styling conventions

### File Storage & Caching *(include if feature reads or writes persistent data)*

- **FS-001**: Draw history MUST be stored via Laravel Storage facade (not database)
- **FS-002**: Download freshness MUST be checked via `CsvDownloadService::isDownloadRequired()`
- **FS-003**: Cache driver MUST remain file-based (no new database dependencies)

### Entertainment Disclaimer *(include for any user-facing feature)*

- **ED-001**: Feature MUST NOT present generated numbers as predictions or forecasts
- **ED-002**: User-facing copy MUST preserve or reference the entertainment-only purpose

### Key Entities *(include if feature involves data)*

- **Game**: Config-driven lottery game (slug, name, logo) from `config/games.php`
- **[Entity]**: [What it represents, key attributes without implementation]

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: [Measurable metric, e.g., "Users can generate numbers for a game in under 3 seconds"]
- **SC-002**: [Measurable metric, e.g., "All new service classes have passing unit tests"]
- **SC-003**: [User satisfaction metric, e.g., "Generated lines display correctly formatted on mobile and desktop"]
- **SC-004**: [Quality metric, e.g., "CI passes on PHP 8.3, 8.4, and 8.5"]

## Assumptions

- Historical draw data is available from existing National Lottery CSV sources
- File-based storage and cache are sufficient (no database required)
- Feature runs within the existing Laravel Sail development environment
- Entertainment-only disclaimer applies to all user-facing output
- [Additional assumption based on feature scope]
