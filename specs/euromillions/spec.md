# Feature Specification: EuroMillions

**Feature Branch**: N/A (migrated from existing code)

**Created**: 2026-07-18

**Status**: Migrated

## User Scenarios & Testing

### User Story 1 - Generate EuroMillions Numbers (Priority: P1)

A user generates suggested EuroMillions lines with 5 main numbers and 2 lucky stars based on historical draw data.

**Independent Test**: Call `EuromillionsGenerate::generate()` and verify via `EuromillionsGenerateTest`.

**Acceptance Scenarios**:

1. **Given** EuroMillions draw history is available, **When** generation runs, **Then** each line has `mainNumbers` (5 balls) and `luckyStars` (2 balls)
2. **Given** stale data, **When** download is triggered, **Then** fresh CSV is fetched from National Lottery API

### Edge Cases

- Download failure handling
- Lines with duplicate numbers within a ball type

## Requirements

- **FR-001**: Download EuroMillions history (`EuromillionsDownload`)
- **FR-002**: Generate lines with main numbers and lucky stars (`EuromillionsGenerate`)
- **FR-003**: Display formatted with `**` separator between main numbers and lucky stars

## Success Criteria

- **SC-001**: `EuromillionsGenerateTest` passes
- **SC-002**: `EuromillionsDownloadTest` passes

## Assumptions

- Also used by EuroMillions Hotpicks (shares download service)
