# Implementation Plan: Auth Scaffold

**Branch**: N/A (migrated) | **Date**: 2026-07-18 | **Spec**: [spec.md](./spec.md)

**Status**: Migrated (dormant)

## Summary

Standard Laravel authentication scaffolding. Present in codebase but not integrated — no routes, no UI, no tests.

## Technical Context

**Controllers**: 5 files in `app/Http/Controllers/Auth/` (~220 lines total)

**Model**: `app/Models/User.php`

**Migrations**: `create_users_table`, `create_password_resets_table`

**Routes**: None registered

## Project Structure

```text
app/Http/Controllers/Auth/
├── ForgotPasswordController.php
├── LoginController.php
├── RegisterController.php
├── ResetPasswordController.php
└── VerificationController.php

app/Models/User.php
database/migrations/2014_10_12_000000_create_users_table.php
database/migrations/2014_10_12_100000_create_password_resets_table.php
```

## Recommendation

Either remove unused scaffold or document as out-of-scope. Activating auth would require routes, views, tests, and a database — conflicting with the file-based architecture principle.
