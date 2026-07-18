# Implementation Plan: Lotto

**Branch**: N/A (migrated) | **Date**: 2026-07-18 | **Spec**: [spec.md](./spec.md)

**Status**: Migrated

## Summary

Lotto game with dedicated download and generate services. Downloads 180-day draw history, analyses ball frequency, and produces suggested 6-number lines.

## Technical Context

**Services**: `LottoDownload.php` (132 lines), `LottoGenerate.php` (267 lines)

**Tests**: `LottoDownloadTest.php`, `LottoGenerateTest.php` (extends shared base cases)

**Config**: `config/games.php` entry with slug `lotto`

**Also used by**: Lotto Hotpicks (shares download service)

## Project Structure

```text
app/Services/Lottery/
├── LottoDownload.php
└── LottoGenerate.php

tests/Unit/Lottery/
├── LottoDownloadTest.php
└── LottoGenerateTest.php
```

## Implementation Phases (Completed)

### Phase A: Download — DONE
### Phase B: Generate — DONE
### Phase C: Tests — DONE
### Phase D: Controller wiring — DONE (via `GameController`)
