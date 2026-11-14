# README (GitHub application)

Generate or update `README.md` from repository-native sources. Keep the result suitable for GitHub contributors and evaluators.

Repository-wide conventions: [AGENTS.md](../../../AGENTS.md).

## Primary inputs

- `composer.json`
- `README.md` (existing)
- `src/`, `tests/`
- `.github/workflows/*.yml`
- `config/games.php`
- `CONTRIBUTING.md`
- `docs/supported-versions.md`
- `LICENSE`

## Rules

1. Treat `composer.json` as the source of truth for PHP requirements and package description.
2. Preserve strong repository-specific content unless files indicate change.
3. State entertainment-only purpose; do not present the app as a forecasting tool.
4. Include PHP + Composer setup (`composer install`, `php -S … -t public`, `vendor/bin/phpunit`, Pint).
5. Note file-based storage (no database required for core features).
6. Link to the public site (`website/content/` or published URL) and maintainer docs in `docs/` where helpful.
7. Use standard Markdown; UK English; PSR-12 style for PHP snippets.
8. Do not name documentation frameworks in README prose.
9. Do not add Spec Kit internals, `specs/`, or `.specify/` unless already present in README.

## Sections to include when justified

- Project name and description
- Requirements (PHP version from `composer.json` and CI)
- Quick start / local development
- Supported games (from `config/games.php` when documenting features)
- Testing and lint commands
- Contributing (brief, link to CONTRIBUTING.md)
- Licence

Omit filler sections unless the repository supports them.
