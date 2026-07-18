# Feature Specification: EuroMillions Hotpicks

**Feature Branch**: N/A (migrated from existing code)

**Created**: 2026-07-18

**Status**: Migrated

## User Scenarios & Testing

### User Story 1 - Generate EuroMillions Hotpicks Numbers (Priority: P1)

A user generates suggested EuroMillions Hotpicks lines based on EuroMillions historical data.

**Independent Test**: Call `EuromillionsHotpicksGenerate::generate()` and verify via `EuromillionsHotpicksGenerateTest`.

**Acceptance Scenarios**:

1. **Given** EuroMillions draw history is available (shared), **When** generation runs, **Then** valid lines are produced
2. **Given** slug `euromillions-hotpicks`, **When** accessed via web, **Then** `EuromillionsDownload` is used for data

## Requirements

- **FR-001**: Extend `EuromillionsGenerate` with hotpicks configuration (`EuromillionsHotpicksGenerate`)
- **FR-002**: Reuse `EuromillionsDownload` for historical data
- **FR-003**: Game name returned as `EuromillionsHotpicks`

## Success Criteria

- **SC-001**: `EuromillionsHotpicksGenerateTest` passes

## Assumptions

- Shares EuroMillions CSV data and download infrastructure
- Smallest generate service at 27 lines (mostly overrides)
