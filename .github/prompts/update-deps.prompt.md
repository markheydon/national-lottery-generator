---
name: Update Dependencies
model: GPT-5.4 mini
agent: 'agent'
description: Run a full repository dependency refresh across Composer, Yarn, runtime pins, CI versions, and versioned docs, then validate with Sail.
argument-hint: Optional scope, e.g. "patch-only", "php only", "js only", "skip github actions", or "skip docs"
---

Update dependencies for this repository.

Use this prompt when you want one intentional dependency refresh across the repo instead of merging a run of individual Dependabot PRs.

Default behaviour:

- Include major upgrades unless the user explicitly narrows scope.
- Treat this as a repo-wide sync task, not just a manifest bump task.
- Prefer Yarn as the JavaScript lockfile authority because this repo uses `yarn.lock`.
- Keep changes focused on dependency-update fallout; do not fix unrelated issues.

## Source-of-truth files

Read these files before making changes:

- `composer.json`
- `composer.lock`
- `package.json`
- `yarn.lock`
- `docker-compose.yml`
- `.devcontainer/Dockerfile`
- `.devcontainer/devcontainer.json`
- `.github/workflows/laravel.yml`
- `.github/workflows/phpmd.yml`
- `.github/workflows/deploy-azure-webapp.yml`
- `.github/workflows/dependency-review.yml`
- `.github/workflows/dependabot-auto-merge.yml`
- `.github/dependabot.yml`
- `README.md`
- `CONTRIBUTING.md`
- `.github/copilot-instructions.md`

## Required outcomes

- Update Composer dependencies and regenerate `composer.lock` as needed.
- Update JavaScript dependencies in `package.json` and regenerate `yarn.lock`.
- Review whether runtime version changes require sync updates in Sail, devcontainer, CI, deployment, or docs.
- Keep GitHub Actions and dependency-management configuration aligned when dependency/runtime changes make that necessary.
- Run the required validation commands and report the results.

## Execution order

1. Confirm scope from the user request.
   If no scope narrowing was provided, perform a full update including major versions.

2. Inspect the current dependency and runtime surfaces.
   Identify direct dependency constraints, lockfiles, pinned PHP versions, pinned Node versions, GitHub Action versions, and any docs that mention version requirements.

3. Prepare the local environment.
   - If `.env` is missing, create it from `.env.example`.
   - Start Sail with `./vendor/bin/sail up -d` before dependency operations.
   - Use the repo's Sail-based workflow for PHP validation and tests.

4. Update PHP dependencies.
   - Prefer Composer-native update commands.
   - Widen constraints in `composer.json` when needed for intended upgrades.
   - Regenerate `composer.lock` in the same change.
   - Keep Laravel and related ecosystem packages coherent when upgrading across majors.

5. Update JavaScript dependencies.
   - Update dependency ranges in `package.json` when needed.
   - Regenerate `yarn.lock` with Yarn; do not introduce `package-lock.json`.
   - Keep the existing build toolchain working unless the requested update scope explicitly includes replacing it.

6. Sync dependent runtime/version pins.
   If the update changes the supported PHP or Node story, keep it aligned across:
   - `composer.json`
   - `docker-compose.yml`
   - `.devcontainer/Dockerfile`
   - `.devcontainer/devcontainer.json`
   - `.github/workflows/laravel.yml`
   - `.github/workflows/phpmd.yml`
   - `.github/workflows/deploy-azure-webapp.yml`
   - `README.md`
   - `CONTRIBUTING.md`
   - `.github/copilot-instructions.md`

7. Review related dependency automation/config.
   Update `.github/dependabot.yml` only if the dependency-management surface has materially changed or existing grouping/policy would become misleading after the refresh.

8. Validate after the update.
   Required checks:
   - `./vendor/bin/sail artisan test`
   - `./vendor/bin/sail pint --test`

   Recommended checks when relevant to the touched dependencies:
   - `composer validate`
   - `composer audit`
   - `yarn install` if needed to refresh `yarn.lock`
   - `yarn production` or `npm run production` to confirm the asset pipeline still builds

9. Repair only dependency-update fallout.
   If validation fails because of the dependency refresh, make the smallest coherent fix and rerun the relevant validation.
   If validation exposes an unrelated pre-existing issue, report it without expanding scope.

## Rules

- Do not assume `composer.json` and `package.json` are the only dependency surfaces.
- Do not leave runtime/version pins inconsistent across local dev, CI, deployment, and docs.
- Do not create `package-lock.json` while this repo remains Yarn-based.
- Do not silently skip major-version changes; either apply them or explain why they were intentionally deferred.
- Do not make unrelated refactors or feature changes.
- Prefer GPT-5 mini only if GPT-5.4 mini is unavailable in the environment.

## Final response

When complete, summarise:

- Which packages were upgraded, including notable major-version jumps.
- Which non-package files changed for runtime/version sync.
- Which validation commands were run and whether they passed.
- Any upgrades intentionally deferred, with reasons.
- Any manual follow-up required before merge or deployment.