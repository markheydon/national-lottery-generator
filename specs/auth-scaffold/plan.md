# Implementation Plan: Auth Scaffold

**Branch**: N/A (migrated) | **Date**: 2026-07-18 | **Spec**: [spec.md](./spec.md)

**Status**: Removed (2026) — deleted during vanilla PHP migration (#205 / #210)

## Summary

Laravel default authentication scaffolding (login, register, password reset, email verification) was present but never wired into routes or UI. It was removed entirely when the app moved off Laravel.

## What was removed

- Auth controllers (`LoginController`, `RegisterController`, etc.)
- `User` Eloquent model
- Users / password-resets migrations
- Related Laravel auth config and Sanctum dependency

## Recommendation

Do not reintroduce auth without an explicit new feature spec. Activating accounts would require routes, views, tests, and a database — conflicting with the file-based, entertainment-only architecture.
