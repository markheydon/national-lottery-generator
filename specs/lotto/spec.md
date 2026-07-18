# Feature Specification: Lotto

**Feature Branch**: N/A (migrated from existing code)

**Created**: 2026-07-18

**Status**: Migrated

## User Scenarios & Testing

### User Story 1 - Generate Lotto Numbers (Priority: P1)

A user generates suggested Lotto lines (6 main numbers from 1–59) based on historical draw frequency analysis.

**Independent Test**: Call `LottoGenerate::generate()` and verify output structure via `LottoGenerateTest`.

**Acceptance Scenarios**:

1. **Given** Lotto draw history CSV is available, **When** generation runs, **Then** output includes `gameName`, `latestDrawDate`, and `lines` with 6 main numbers per line
2. **Given** CSV is missing or stale, **When** `LottoDownload::download()` is called, **Then** data is fetched from the National Lottery API

### Edge Cases

- Empty or corrupt CSV file
- Download failure (network error, simulated via `$failDownload` flag)

## Requirements

- **FR-001**: Download Lotto history from National Lottery API (`LottoDownload::HISTORY_DOWNLOAD_URL`)
- **FR-002**: Parse CSV into structured draw arrays (`LottoDownload::readLottoDrawHistory()`)
- **FR-003**: Generate multiple suggested lines using frequency analysis (`LottoGenerate::generate()`)
- **FR-004**: Support `LOTTERY_DOWNLOAD_URL_LOTTO` env override for testing

## Success Criteria

- **SC-001**: `LottoGenerateTest` passes (output structure, line counts, ball ranges)
- **SC-002**: `LottoDownloadTest` passes (download OK, failure handling)

## Assumptions

- Lotto uses 6 balls from 1–59 plus a bonus ball in historical data
- Draw history URL: `api-dfe.national-lottery.co.uk/draw-game/results/1/download`
