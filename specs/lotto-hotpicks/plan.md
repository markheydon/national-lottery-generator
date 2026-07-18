# Implementation Plan: Lotto Hotpicks

**Branch**: N/A (migrated) | **Date**: 2026-07-18 | **Spec**: [spec.md](./spec.md)

**Status**: Migrated

## Summary

Lotto Hotpicks variant — extends `LottoGenerate` with 5 balls. Shares `LottoDownload` for data.

## Technical Context

**Services**: `LottoHotpicksGenerate.php` (51 lines, extends `LottoGenerate`)

**Download**: Reuses `LottoDownload` via `Game::getDownloader()` slug mapping

**Tests**: `LottoHotpicksGenerateTest.php`

**Config**: slug `lotto-hotpicks`

## Implementation Phases (Completed)

All phases complete.
