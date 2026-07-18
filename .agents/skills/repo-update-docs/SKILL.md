---
name: repo-update-docs
description: Refresh this repository's README.md and public docs/ from repository-native sources.
---

# Update Repository Documentation

Maintain documentation for the National Lottery Generator Laravel application. Use repository-native facts only — do not invent unsupported claims. Keep wording in UK English.

## When to Use

- After behaviour, requirements, or supported versions change
- When README.md or public docs in `docs/` are stale vs the codebase
- When preparing a release or dependency refresh that affects user-facing statements

## Scope

| Area | Audience | Edit? |
|------|----------|-------|
| `README.md` | Contributors and new users | Yes |
| `docs/` | End users (GitHub Pages) | Yes |
| `docs-internal/` | Maintainers | No, unless explicitly requested |
| Application code | — | No |

## README.md Rules

Refresh `README.md` from these source-of-truth files:

- `composer.json` — PHP version, Laravel version, description
- `README.md` — preserve strong existing content unless stale
- `.github/workflows/laravel.yml` — CI PHP matrix
- `.github/workflows/deploy-azure-webapp.yml` — deployment target
- `config/games.php` — supported lottery games
- `routes/web.php` — available routes
- `CONTRIBUTING.md` — contribution pointers
- `docs-internal/supported-versions.md` — version policy

**README content guidelines:**

- Contributor-friendly overview: what the app does, quick start, development commands
- State entertainment-only purpose; do not present as a forecasting tool
- Include Sail-based setup (`./vendor/bin/sail up -d`, `artisan test`, `pint`)
- Note file-based storage (no database required for core features)
- Link to public docs in `docs/` and maintainer docs in `docs-internal/`
- Use standard Markdown; keep opening sections scannable
- Do not add Spec Kit internals, `specs/`, or `.specify/` unless already present

## docs/ Rules (Public, GitHub Pages)

Keep public docs in sync with current app behaviour. Inspect before editing:

- `config/games.php`
- `routes/web.php`
- `resources/views/games/` — user-facing UI
- `docs/README.md`, `docs/index.md`, `docs/getting-started.md`
- `docs/how-it-works.md`, `docs/faq.md`, `docs/developer-reference.md`

**Page purposes (one primary purpose per page):**

- **Start here** — what the app does and how to use it quickly
- **Task pages** — step-by-step guidance for common actions
- **Explanation pages** — behaviour and limitations in plain language
- **FAQ** — concise answers to common questions

**Public docs guidelines:**

- End-user tone: friendly, practical, clear next steps
- Avoid developer implementation details unless they directly help users
- Preserve entertainment-only disclaimer and responsible gambling messaging
- Use relative links for in-repository references
- Compatible with GitHub Pages/Jekyll (`docs/_config.yml`)
- Make minimal, high-signal edits — avoid broad rewrites
- Add a new page only when there is clear evidence-based value

## Update Strategy

1. Read source-of-truth files listed above
2. Refresh `README.md` if requirements, setup, or tooling statements are stale
3. Compare each `docs/` page against current behaviour
4. Update only pages that are incomplete or incorrect
5. Do not modify application code, workflows, or deployment config

## Output

When complete, summarise:

- Which documentation files changed
- Key facts synchronised (PHP versions, games, commands, CI, deployment)
- Any remaining documentation gaps requiring user direction
