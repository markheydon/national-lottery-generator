# AGENTS.md

Agent orientation for the National Lottery Generator. This vanilla PHP application analyses UK National Lottery historical draw data and generates playful number suggestions for entertainment. It is not a forecasting tool.

## Architecture

Minimal PHP front controller with plain PHP templates and file-based storage (no database for core features).

| Area | Path |
|------|------|
| Lottery services | `src/Services/Lottery/` — per-game `*Download` and `*Generate` classes; `GameGenerateRegistry` (slug dispatch); `DrawHistoryService` (CSV availability) |
| HTTP layer | `src/Http/GameController.php`, `src/Http/Application.php` |
| Front controller | `public/index.php` |
| Game config | `config/games.php` — slug, name, logo per game |
| Views | `templates/games/` |
| Routes | `public/index.php` — `/` and `/game/{slug}/generate` |
| Unit tests | `tests/Unit/Lottery/` |
| Feature tests | `tests/Feature/` |
| E2E tests | `tests/e2e/` (Playwright) |
| Azure deploy | `deploy/bicep/` (Bicep), `deploy/README.md`, `docs/azure-infrastructure.md`; app CD `.github/workflows/deploy-azure-webapp.yml`; infra `.github/workflows/deploy-azure-infrastructure.yml` (manual) |
| Azure nginx helper | `deploy/nginx-default` (also adaptable for local nginx; see `deploy/README.md`) |

Hotpicks variants (`lotto-hotpicks`, `euromillions-hotpicks`) share their parent game's download service. See `src/Game.php::getDownloader()`.

Draw history CSVs are stored in `storage/app/lottery/`. Downloads refresh when files are older than 24 hours (`CsvDownloadService`). Refreshes back up the current file before replace; failed downloads leave the previous file in place when possible. Optional env: `LOTTERY_DOWNLOAD_TIMEOUT` (default 30s), `LOTTERY_BACKUP_MAX` (default 5). Unavailable draw history renders `templates/games/unavailable.php` (HTTP 503).

## Essential Commands

```bash
composer install                     # Install dependencies
php -S 0.0.0.0:8000 -t public        # Start dev server
vendor/bin/phpunit                   # Run PHPUnit
vendor/bin/pint --test               # Check PSR-12
vendor/bin/pint                      # Fix code style
```

Playwright E2E (`tests/e2e/`): runs in GitHub Actions CI on pull requests only (`.github/workflows/ci.yml`). The Dev Container does not install Node or Playwright.

## Adding a New Game

1. Add entry to `config/games.php` (slug, name, logo)
2. Create `src/Services/Lottery/{Game}Download.php` and `{Game}Generate.php`
3. Add unit tests in `tests/Unit/Lottery/`
4. Register the slug in `GameGenerateRegistry` (`src/Services/Lottery/GameGenerateRegistry.php`)

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

This repo typically uses SSH commit signing (`gpg.format=ssh`, `user.signingkey` set to the SSH public key). If `git commit` fails because of signing, treat that as a hard failure — do not disable signing to make it pass.

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

Spec Kit commands for Cursor live under `.cursor/skills/speckit-*`. This repository does not maintain GitHub Copilot agent or prompt files under `.github/agents/` or `.github/prompts/`. Supported Spec Kit integration: **cursor-agent** (see `.specify/integrations/cursor-agent.manifest.json`). Upgrade or refresh bundled skills and `.specify/` scripts with:

```bash
specify init --here --force --integration cursor-agent --script sh
```

Existing `.specify/memory/constitution.md` is preserved on upgrade; review diffs before committing.

## Documentation conventions

- Keep `README.md` suitable for contributors and quick evaluation.
- Public user docs live in `website/content/` (Hugo site on GitHub Pages at `lottery.markheydon.me.uk`).
- Maintainer notes live in `docs/`.
- Use concise UK English.
- User-facing copy in `README.md` and `website/content/` should not explain documentation methodology or name authoring frameworks; use agent skills and `docs/` for methodology.
- Preserve the entertainment-only disclaimer on user-facing pages; do not present the app as a forecasting tool.

## Repository source of truth

When writing or updating code or documentation, prioritise:

- `composer.json` for PHP requirements, description, and Composer scripts
- `config/games.php` for supported games
- `src/` and `tests/` for behaviour and usage evidence
- `.github/workflows/ci.yml` for CI and PHP matrix
- `.github/workflows/deploy-azure-webapp.yml` and `deploy/bicep/main.bicepparam` for production PHP runtime on Azure
- `docs/azure-infrastructure.md` and `deploy/README.md` for Azure deploy and Bicep
- `docs/supported-versions.md` for version policy
- `docs/development-setup.md` for maintainer setup
- `website/hugo.yaml` and `.github/workflows/hugo.yml` for public site build and deploy

Do not invent behaviour, compatibility claims, or roadmap commitments unsupported by repository files.

## Task routing

- **Code** (`src/`, `tests/`, `templates/`): PHP conventions and change discipline in this file.
- **User documentation** (`README.md`, `website/content/`): `project-documentation` skill.
- **Maintainer markdown** (`docs/**/*.md`): `project-documentation` skill when explicitly in scope.
- **Pre-merge review**: `code-review` skill or `/loop-code-review`.
- **Dependabot config**: `/update-dependabot` (`.cursor/commands/update-dependabot.md`).

## Project skills

Use these skills for specialised workflows:

| Skill | When to use |
|-------|-------------|
| `project-documentation` | README, `website/content/`, `docs/` when requested (Diátaxis placement via skill) |
| `code-review` | Branch, PR, or local diff review before merge |

Skills live in `.agents/skills/<skill-name>/SKILL.md` (agent-agnostic). Cursor slash commands live in `.cursor/commands/`.

## Pointers

- Human contributors: `CONTRIBUTING.md`
- Maintainer setup: `docs/development-setup.md`
- Supported PHP versions: `docs/supported-versions.md`
- Public user site: `website/` (preview: `./scripts/invoke-hugo-site.sh preview`)
- Maintainer docs: `docs/`
- Open follow-ups: #187 (Set For Life tests), #190 (HTTP feature test)

## Cursor Cloud specific instructions

The Cloud VM runs PHP 8.3 and Composer directly (no Docker/Sail). Use:

- Serve: `php -S 0.0.0.0:8000 -t public`
- Test: `vendor/bin/phpunit`
- Lint: `vendor/bin/pint --test` (fix with `vendor/bin/pint`)

Non-obvious caveats:

- Frontend assets are committed under `public/css` and `public/js`; there is no npm build step.
- The `/game/{slug}/generate` routes fetch live draw-history CSVs from the National Lottery API on
  first use (outbound HTTPS required), caching them to `storage/app/lottery/` for 24h. After the cache
  is warm the pages render offline.
- Playwright E2E specs live in `tests/e2e/`; CI runs `npm run test:e2e` — not in the Dev Container.
- Preview the public Hugo site with `./scripts/invoke-hugo-site.sh preview` (Docker/Podman via devcontainer Docker feature).
