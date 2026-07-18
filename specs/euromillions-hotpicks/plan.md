# Implementation Plan: EuroMillions Hotpicks

**Branch**: N/A (migrated) | **Date**: 2026-07-18 | **Spec**: [spec.md](./spec.md)

**Status**: Migrated

## Summary

EuroMillions Hotpicks variant — extends `EuromillionsGenerate`. Shares `EuromillionsDownload` for data.

## Technical Context

**Services**: `EuromillionsHotpicksGenerate.php` (27 lines, extends `EuromillionsGenerate`)

**Download**: Reuses `EuromillionsDownload` via `Game::getDownloader()` slug mapping

**Tests**: `EuromillionsHotpicksGenerateTest.php`

**Config**: slug `euromillions-hotpicks`

## Implementation Phases (Completed)

All phases complete.
