# Feature Specification: Auth Scaffold

**Feature Branch**: N/A (migrated from existing code)

**Created**: 2026-07-18

**Status**: Removed (2026) — Laravel auth scaffold deleted during vanilla PHP migration (#205)

## User Scenarios & Testing

### User Story 1 - User Authentication (Priority: P3)

Laravel default authentication scaffolding (login, register, password reset, email verification) — **removed**.

**Independent Test**: No auth routes or controllers remain in the application.

**Acceptance Scenarios**:

1. **Given** no auth routes, **When** a user visits `/login`, **Then** a 404 is returned
2. **Given** the Laravel migration (#205), **When** inspecting the codebase, **Then** no auth controllers, User model, or database migrations remain

## Requirements

- **FR-001**: Auth scaffold removed — no dormant Laravel auth code
- **FR-002**: No database dependency introduced for core features

## Success Criteria

- **SC-001**: Application runs without Laravel auth packages or migrations

## Gaps Identified

- None — feature intentionally removed as out of scope for the entertainment-only lottery app

## Assumptions

- Left over from `laravel new` scaffolding
- Not part of the lottery generator user flow
- Removed during #205 migration to minimal vanilla PHP
