# Feature Specification: Thunderball

**Feature Branch**: N/A (migrated from existing code)

**Created**: 2026-07-18

**Status**: Migrated

## User Scenarios & Testing

### User Story 1 - Generate Thunderball Numbers (Priority: P1)

A user generates suggested Thunderball lines with 5 main numbers and 1 thunderball based on historical draw data.

**Independent Test**: Call `ThunderballGenerate::generate()` and verify via `ThunderballGenerateTest`.

**Acceptance Scenarios**:

1. **Given** Thunderball draw history is available, **When** generation runs, **Then** each line has `mainNumbers` (5 balls) and `thunderball` (1 ball)
2. **Given** stale data, **When** download is triggered, **Then** fresh CSV is fetched

## Requirements

- **FR-001**: Download Thunderball history (`ThunderballDownload`)
- **FR-002**: Generate lines with main numbers and thunderball (`ThunderballGenerate`)
- **FR-003**: Display formatted with `**` separator between main numbers and thunderball

## Success Criteria

- **SC-001**: `ThunderballGenerateTest` passes
- **SC-002**: `ThunderballDownloadTest` passes
