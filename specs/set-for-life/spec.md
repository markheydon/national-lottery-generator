# Feature Specification: Set For Life

**Feature Branch**: N/A (migrated from existing code)

**Created**: 2026-07-18

**Status**: Migrated

## User Scenarios & Testing

### User Story 1 - Generate Set For Life Numbers (Priority: P1)

A user generates suggested Set For Life lines with 5 main numbers and 1 life ball based on historical draw data.

**Independent Test**: Call `SetForLifeGenerate::generate()` via `SetForLifeGenerateTest`.

**Acceptance Scenarios**:

1. **Given** Set For Life draw history is available, **When** generation runs, **Then** each line has `mainNumbers` (5 balls) and `lifeBall` (1 ball)
2. **Given** stale data, **When** download is triggered, **Then** fresh CSV is fetched

## Requirements

- **FR-001**: Download Set For Life history (`SetForLifeDownload`)
- **FR-002**: Generate lines with main numbers and life ball (`SetForLifeGenerate`)
- **FR-003**: Display formatted with `**` separator between main numbers and life ball

## Success Criteria

- **SC-001**: Generate page renders correctly at `/game/set-for-life/generate`
- **SC-002**: Unit tests cover download and generate services
