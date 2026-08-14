# Supported Versions Policy

## Purpose

This policy defines how supported PHP versions are chosen, enforced, and
verified in this repository.

## Version Policy

- The repository supports the PHP versions declared in `composer.json`.
- Development and deployment defaults should use the newest supported PHP
  version when practical.

## Current Policy Baseline

- Supported PHP compatibility range: `8.3` to `8.5`
- Default development runtime: `8.5` (or host PHP 8.3+ locally)
- Default deployment build/runtime target: `8.5` when the hosting platform
  supports it

## Enforcement Rules

- Composer constraints must not narrow PHP support without an explicit policy change.
- Dependency updates must preserve installability on the lowest supported PHP version.
- CI testing must cover the full supported PHP range.
- Static analysis may run on a single default runtime version.

## Dependency Update Guardrails

- When refreshing dependencies, resolve and validate using the lowest
  supported PHP version to avoid accidental compatibility drift.
- Keep Composer platform configuration aligned with the lowest supported PHP
  version so lockfile updates remain compatible.
- If an upstream package raises its minimum PHP version, decide whether to pin,
  replace, or approve a policy change before merging.

## Related Files

- `composer.json`
- `composer.lock`
- `.github/workflows/ci.yml`
- `.github/workflows/deploy-azure-webapp.yml`
- `.github/workflows/phpmd.yml`
- `.devcontainer/Dockerfile`
- `AGENTS.md`

## PHP Version Update Checklist

When changing the supported PHP version:

1. Update this policy document first
2. Update `composer.json` PHP requirement and platform config
3. Update `.devcontainer/Dockerfile` if applicable
4. Update `README.md` version badges and requirements
5. Update GitHub workflow PHP versions in `ci.yml`, `deploy-azure-webapp.yml`, and `phpmd.yml`
6. Update `AGENTS.md` if version statements are present
7. Run the full test suite on the lowest and highest supported PHP versions
