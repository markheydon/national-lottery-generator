# Document placement guide

Use this guide after choosing the Diátaxis document type in [diataxis.md](diataxis.md).

## Three channels

This repository separates documentation by audience and publish path:

| Channel | Path | Primary audience |
| --- | --- | --- |
| Repository front door | `README.md` | Contributors, evaluators, quick start |
| Public documentation | `website/content/` | End users (Hugo site on GitHub Pages) |
| Maintainer notes | `docs/` | Maintainers and coding agents |

Ground the choice in [AGENTS.md](../../../AGENTS.md) and existing pages under `website/content/`.

## Public site (`website/content/`)

- Built with Hugo (see `website/hugo.yaml`, `website/static/CNAME`).
- Typical pages: `_index.md`, `guides/getting-started.md`, `guides/how-it-works.md`, `guides/faq.md`.
- Match Diátaxis type to page role where possible (tutorial vs how-to vs explanation vs reference).
- Preserve entertainment-only disclaimer and responsible gambling messaging on user-facing pages.
- Use site-relative links (for example `/guides/faq/`).

See [hugo-site.md](hugo-site.md) for Hugo and page conventions.

## Put content in `README.md` when

- It explains what the application is, requirements, installation, and minimal usage.
- The primary audience is contributors or people evaluating the repo.
- The content must appear on the repository front page.

Follow [readme-github.md](readme-github.md).

## Put content in `website/content/` when

- The audience is end users of the web app (how to use games, what to expect, limitations).
- The content should be friendly and practical, not maintainer policy.
- It belongs on the public documentation site.

## Put content in `docs/` when

- The audience is maintainers (development setup, supported PHP versions, deployment internals).
- The user or task explicitly requests maintainer documentation.
- It supports agent workflows and need not be rewritten for end users.

## Decision rules

- Favour `README.md` for orientation, dev setup, and links to the public site.
- Favour `website/content/` for substantive end-user guidance and explanations.
- Favour `docs/` for version policy, Azure deploy details, and contributor-only setup.
- Avoid duplicating the same guidance in README and the public site; link instead.
- If a README section becomes long or user-guide-like, move detail into `website/content/` and leave a short summary plus link in the README.
- Do not modify application code, workflows, or deployment config unless the task explicitly includes it.

## Source-of-truth files

When refreshing documentation, read relevant files before editing:

- `composer.json` — PHP version, description
- `.github/workflows/ci.yml` — CI PHP matrix
- `.github/workflows/hugo.yml` — public site deploy
- `.github/workflows/deploy-azure-webapp.yml` — application deployment
- `.github/workflows/deploy-azure-infrastructure.yml` and `deploy/bicep/` — Azure infrastructure (Bicep)
- `docs/azure-infrastructure.md` — maintainer Azure/OIDC notes
- `config/games.php` — supported lottery games
- `public/index.php` — routes
- `templates/games/` — user-facing UI and disclaimers
- `docs/supported-versions.md` — version policy