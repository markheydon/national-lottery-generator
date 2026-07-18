# Feature Specification: Auth Scaffold

**Feature Branch**: N/A (migrated from existing code)

**Created**: 2026-07-18

**Status**: Migrated

## User Scenarios & Testing

### User Story 1 - User Authentication (Priority: P3)

Laravel default authentication scaffolding (login, register, password reset, email verification) — **not wired into the application**.

**Independent Test**: No routes register these controllers; feature is dormant.

**Acceptance Scenarios**:

1. **Given** no auth routes in `routes/web.php`, **When** a user visits `/login`, **Then** a 404 is returned
2. **Given** auth controllers exist, **When** routes are added in future, **Then** standard Laravel auth flow would be available

## Requirements

- **FR-001**: Laravel default auth controllers present (`LoginController`, `RegisterController`, etc.)
- **FR-002**: User Eloquent model exists (`app/Models/User.php`)
- **FR-003**: Database migrations for users and password resets exist but are unused by core app

## Success Criteria

- **SC-001**: Controllers exist and follow Laravel conventions

## Gaps Identified

- ⚠️ No routes registered — auth is completely unused
- ⚠️ No tests for auth controllers
- ⚠️ Introduces database dependency if activated (conflicts with file-based core architecture)
- ⚠️ Consider removing or explicitly scoping if not planned

## Assumptions

- Left over from `laravel new` scaffolding
- Not part of the lottery generator user flow
