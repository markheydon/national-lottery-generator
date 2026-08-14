# AGENTS.md

Agent orientation for the National Lottery Generator. This vanilla PHP application analyses UK National Lottery historical draw data and generates playful number suggestions for entertainment. It is not a forecasting tool.

## Architecture

Minimal PHP front controller with plain PHP templates and file-based storage (no database for core features).

| Area | Path |
|------|------|
| Lottery services | `src/Services/Lottery/` — per-game `*Download` and `*Generate` classes |
| HTTP layer | `src/Http/GameController.php`, `src/Http/Application.php` |
| Front controller | `public/index.php` |
| Game config | `config/games.php` — slug, name, logo per game |
| Views | `templates/games/` |
| Routes | `public/index.php` — `/` and `/game/{slug}/generate` |
| Unit tests | `tests/Unit/Lottery/` |
| Feature tests | `tests/Feature/` |
| E2E tests | `tests/e2e/` (Playwright) |
| Azure nginx helper | `deploy/nginx-default` (also adaptable for local nginx; see `deploy/README.md`) |

Hotpicks variants (`lotto-hotpicks`, `euromillions-hotpicks`) share their parent game's download service. See `src/Game.php::getDownloader()`.

Draw history CSVs are stored in `storage/app/lottery/`. Downloads refresh when files are older than 24 hours (`CsvDownloadService`). Optional env: `LOTTERY_DOWNLOAD_TIMEOUT` (default 30s).

## Essential Commands

```bash
composer install                     # Install dependencies
php -S 0.0.0.0:8000 -t public        # Start dev server
vendor/bin/phpunit                   # Run PHPUnit
vendor/bin/pint --test               # Check PSR-12
vendor/bin/pint                      # Fix code style
```

Playwright E2E (Dev Container installs deps and Chromium during create):

```bash
npm run test:e2e
```

On a host PHP install (no Dev Container): `npm ci`, `npm run test:e2e:install`, then `npm run test:e2e`.

## Adding a New Game

1. Add entry to `config/games.php` (slug, name, logo)
2. Create `src/Services/Lottery/{Game}Download.php` and `{Game}Generate.php`
3. Add unit tests in `tests/Unit/Lottery/`
4. Wire dispatch in `GameController` (refactor tracked in #189)

## Constraints

- Entertainment-only disclaimer must remain on user-facing pages
- No database dependency for core features without explicit scoping
- PSR-12 via Laravel Pint (standalone linter) — run before committing
- PHPUnit tests required for new or modified lottery logic
- Business logic belongs in `src/Services/Lottery/`, not the front controller
- **Signed commits are mandatory** — see [Git commit signing](#git-commit-signing) below

## Git commit signing

`main` is protected by a ruleset that requires **every commit** on a pull request to be signed and verified. Unsigned commits block merge (`mergeStateStatus: BLOCKED`). This applies to all commits on the branch, not only the tip.

Agents MUST:

- Keep signing enabled (`commit.gpgsign=true`). Never pass `--no-gpg-sign`, never run `git -c commit.gpgsign=false commit`, and never change repo or global git config to disable signing.
- **Never bypass signing** when a commit fails because the SSH signing key is unavailable (e.g. `ssh-add` not run in the session). Fix the environment instead — unsigned commits are not acceptable.
- After rebase, cherry-pick, or amend, confirm every commit on the branch is signed before pushing. Replay unsigned history with `git rebase --exec 'git commit --amend --no-edit -n -S' <base>` when needed.
- If signing fails, stop and report the error. Do not fall back to unsigned commits or skip the commit.

Before committing, verify the signing key is loaded:

```bash
ssh-add -l                    # must list your key; if empty, run ssh-add
git commit --allow-empty -S -m "signing test"
git reset HEAD~1              # discard test commit
```

This repo typically uses SSH commit signing (`gpg.format=ssh`, `user.signingkey` set to the SSH public key). If Spec Kit auto-commit or another tool fails on `git commit` because of signing, treat that as a hard failure — do not disable signing to make it pass.

## Spec Kit / SDD

This project uses [Spec Kit](https://github.com/github/spec-kit) for spec-driven development.

| Resource | Purpose |
|----------|---------|
| `.specify/memory/constitution.md` | Binding project principles |
| `specs/` | Feature specs, plans, and tasks |
| `.cursor/skills/speckit-*` | Cursor slash commands for SDD workflow |

### Feature specs (migrated)

| Feature | Path |
|---------|------|
| Core platform | `specs/lottery-core/` |
| Lotto | `specs/lotto/` |
| EuroMillions | `specs/euromillions/` |
| Thunderball | `specs/thunderball/` |
| Set For Life | `specs/set-for-life/` |
| Lotto Hotpicks | `specs/lotto-hotpicks/` |
| EuroMillions Hotpicks | `specs/euromillions-hotpicks/` |
| Auth scaffold (removed) | `specs/auth-scaffold/` |

New features: use `/speckit-specify` on a Spec Kit numbered branch (`001-slug`).

## Maintaining Documentation

Use `.agents/skills/repo-update-docs/SKILL.md` to refresh `README.md` and public `docs/`. Do not edit `docs-internal/` unless asked.

## Pointers

- Human contributors: `CONTRIBUTING.md`
- Maintainer setup: `docs-internal/development-setup.md`
- Supported PHP versions: `docs-internal/supported-versions.md`
- Public user docs: `docs/`
- Open follow-ups: #187 (Set For Life tests), #188 (constitution hotpicks note), #189 (GameController refactor), #190 (HTTP feature test)

## Cursor Cloud specific instructions

The Cloud VM runs PHP 8.3, Composer, and Node directly (no Docker/Sail). Use:

- Serve: `php -S 0.0.0.0:8000 -t public`
- Test: `vendor/bin/phpunit`
- Lint: `vendor/bin/pint --test` (fix with `vendor/bin/pint`)

Non-obvious caveats:

- Frontend assets are committed under `public/css` and `public/js`; there is no npm build step.
- The `/game/{slug}/generate` routes fetch live draw-history CSVs from the National Lottery API on
  first use (outbound HTTPS required), caching them to `storage/app/lottery/` for 24h. After the cache
  is warm the pages render offline.
- Playwright E2E tests live in `tests/e2e/`; run `npm run test:e2e` after `npm ci` and `npm run test:e2e:install` (Dev Container does this during create).
