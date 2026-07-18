---

description: "Task list template for feature implementation"
---

# Tasks: [FEATURE NAME]

**Input**: Design documents from `/specs/[###-feature-name]/`

**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Include test tasks for any lottery logic changes (per constitution). Skip test tasks only for purely cosmetic/UI changes with no service impact.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

This is a Laravel monolith. All paths are relative to the repository root:

- **Services**: `app/Services/Lottery/`
- **Controllers**: `app/Http/Controllers/`
- **Models**: `app/Models/`
- **Config**: `config/`
- **Views**: `resources/views/games/`
- **Routes**: `routes/web.php`
- **Tests**: `tests/Unit/Lottery/`, `tests/Feature/`
- **Assets**: `resources/js/`, `resources/sass/` (compiled to `public/`)

## Quality Commands

```bash
# Run tests
./vendor/bin/sail artisan test

# Check code style
./vendor/bin/sail pint --test

# Fix code style
./vendor/bin/sail pint

# Compile frontend assets (if SCSS/JS changed)
npm run dev        # development
npm run prod       # production
```

<!--
  ============================================================================
  IMPORTANT: The tasks below are SAMPLE TASKS for illustration purposes only.

  The /speckit-tasks command MUST replace these with actual tasks based on:
  - User stories from spec.md (with their priorities P1, P2, P3...)
  - Feature requirements from plan.md
  - Entities from data-model.md
  - Endpoints from contracts/

  Tasks MUST be organized by user story so each story can be:
  - Implemented independently
  - Tested independently
  - Delivered as an MVP increment

  DO NOT keep these sample tasks in the generated tasks.md file.
  ============================================================================
-->

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Verify development environment and dependencies

- [ ] T001 Verify Laravel Sail is running (`./vendor/bin/sail up -d`)
- [ ] T002 Confirm `.env` is configured (copy from `.env.example` if needed)
- [ ] T003 [P] Run existing test suite to establish baseline (`./vendor/bin/sail artisan test`)

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [ ] T004 Add or update game entry in `config/games.php` (slug, name, logo)
- [ ] T005 [P] Create download service in `app/Services/Lottery/{Game}Download.php`
- [ ] T006 [P] Create generate service in `app/Services/Lottery/{Game}Generate.php`
- [ ] T007 Wire game dispatch in `app/Http/Controllers/GameController.php`
- [ ] T008 Add route in `routes/web.php` if new URL pattern needed

**Checkpoint**: Foundation ready — user story implementation can now begin

---

## Phase 3: User Story 1 - [Title] (Priority: P1) 🎯 MVP

**Goal**: [Brief description of what this story delivers]

**Independent Test**: [How to verify this story works on its own]

### Tests for User Story 1

- [ ] T009 [P] [US1] Unit test for generate service in `tests/Unit/Lottery/{Game}GenerateTest.php`
- [ ] T010 [P] [US1] Unit test for download service in `tests/Unit/Lottery/{Game}DownloadTest.php`

### Implementation for User Story 1

- [ ] T011 [US1] Implement generate logic in `app/Services/Lottery/{Game}Generate.php`
- [ ] T012 [US1] Implement download logic in `app/Services/Lottery/{Game}Download.php`
- [ ] T013 [US1] Add controller method or extend dispatch in `app/Http/Controllers/GameController.php`
- [ ] T014 [US1] Create or update Blade view in `resources/views/games/generate.blade.php`
- [ ] T015 [US1] Run tests (`./vendor/bin/sail artisan test`) and Pint (`./vendor/bin/sail pint --test`)

**Checkpoint**: User Story 1 fully functional and testable independently

---

## Phase 4: User Story 2 - [Title] (Priority: P2)

**Goal**: [Brief description of what this story delivers]

**Independent Test**: [How to verify this story works on its own]

### Tests for User Story 2

- [ ] T016 [P] [US2] Unit test in `tests/Unit/Lottery/{Game}GenerateTest.php`

### Implementation for User Story 2

- [ ] T017 [US2] Implement service changes in `app/Services/Lottery/`
- [ ] T018 [US2] Update view in `resources/views/games/`
- [ ] T019 [US2] Run tests and Pint

**Checkpoint**: User Stories 1 and 2 both work independently

---

## Phase 5: User Story 3 - [Title] (Priority: P3)

**Goal**: [Brief description of what this story delivers]

**Independent Test**: [How to verify this story works on its own]

### Tests for User Story 3

- [ ] T020 [P] [US3] Unit test in `tests/Unit/Lottery/`

### Implementation for User Story 3

- [ ] T021 [US3] Implement changes in `app/Services/Lottery/`
- [ ] T022 [US3] Update views or assets as needed
- [ ] T023 [US3] Run tests and Pint

**Checkpoint**: All user stories independently functional

---

[Add more user story phases as needed, following the same pattern]

---

## Phase N: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories

- [ ] TXXX [P] Update public docs in `docs/` if user-facing behaviour changed
- [ ] TXXX [P] Update maintainer docs in `docs-internal/` if setup changed
- [ ] TXXX Compile production assets (`npm run prod`) if SCSS/JS changed
- [ ] TXXX Verify entertainment-only disclaimer is present in user-facing output
- [ ] TXXX Final test run (`./vendor/bin/sail artisan test`) and Pint check
- [ ] TXXX Run quickstart.md validation

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — can start immediately
- **Foundational (Phase 2)**: Depends on Setup — BLOCKS all user stories
- **User Stories (Phase 3+)**: All depend on Foundational phase completion
  - User stories can proceed in parallel or sequentially (P1 → P2 → P3)
- **Polish (Final Phase)**: Depends on all desired user stories being complete

### Within Each User Story

- Tests MUST be written and FAIL before implementation (for lottery logic)
- Services before controllers
- Controllers before views
- Core implementation before integration
- Run `./vendor/bin/sail artisan test` and `./vendor/bin/sail pint --test` before marking complete

### Parallel Opportunities

- Download and Generate service creation (T005, T006) can run in parallel
- Unit tests for download and generate (T009, T010) can run in parallel
- Documentation updates in Polish phase can run in parallel

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational
3. Complete Phase 3: User Story 1
4. **STOP and VALIDATE**: `./vendor/bin/sail artisan test`
5. Deploy/demo if ready

### Incremental Delivery

1. Setup + Foundational → Foundation ready
2. User Story 1 → Test → Deploy (MVP)
3. User Story 2 → Test → Deploy
4. Each story adds value without breaking previous stories

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- All lottery logic changes require unit tests per constitution
- No database migrations needed for core features (file-based storage)
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
