# Feature Specification: Lotto Hotpicks

**Feature Branch**: N/A (migrated from existing code)

**Created**: 2026-07-18

**Status**: Migrated

## User Scenarios & Testing

### User Story 1 - Generate Lotto Hotpicks Numbers (Priority: P1)

A user generates suggested Lotto Hotpicks lines with 5 numbers (pick-5 variant of Lotto).

**Independent Test**: Call `LottoHotpicksGenerate::generate()` and verify via `LottoHotpicksGenerateTest`.

**Acceptance Scenarios**:

1. **Given** Lotto draw history is available (shared with Lotto), **When** generation runs, **Then** each line has 5 `lottoBalls`
2. **Given** the game slug is `lotto-hotpicks`, **When** accessed via web, **Then** `LottoDownload` is used for data (shared download)

## Requirements

- **FR-001**: Extend `LottoGenerate` with 5-ball configuration (`LottoHotpicksGenerate`)
- **FR-002**: Reuse `LottoDownload` for historical data (no separate download class)
- **FR-003**: Game name returned as `LottoHotpicks`

## Success Criteria

- **SC-001**: `LottoHotpicksGenerateTest` passes

## Assumptions

- Shares Lotto CSV data and download infrastructure
- Overrides `getNumOfBalls()` to return 5 instead of 6
