---
name: project-documentation
description: >-
  Write or update repository documentation (README, docs/, docs-internal/) using
  Diátaxis and project context (AGENTS.md, composer.json). Use when drafting
  tutorials, how-tos, reference, explanations, or choosing README vs docs/
  vs maintainer placement.
---

# Project documentation

## Instructions

1. Pick the Diátaxis document type and workflow in [references/diataxis.md](references/diataxis.md).
2. Read context before drafting: [AGENTS.md](../../../AGENTS.md), `composer.json`, relevant `src/` and `tests/`, and `docs-internal/` when the topic touches versions, setup, or policy.
3. Choose the file location using [references/document-placement.md](references/document-placement.md).
4. For `README.md`, follow [references/readme-github.md](references/readme-github.md). For public `docs/`, follow [references/github-pages-docs.md](references/github-pages-docs.md).
5. Use a starter from [assets/](assets/) when it fits the document type.
6. Finish with [references/review-checklist.md](references/review-checklist.md).

## Boundaries

- Edit `docs-internal/` only when the user or task explicitly includes maintainer docs.
- When context files and existing docs disagree, flag the mismatch.

## Writing rules

- Concise, task-oriented prose; reflect the real repo layout.
- Prefer short README sections with links to `docs/` for end-user depth.
- User-facing copy in `README.md` and `docs/` must not name documentation frameworks (Diátaxis, quadrants, and similar). Section titles such as Getting Started or FAQ are fine.
- Use PSR-12 style for PHP snippets in user-facing docs.
- State entertainment-only purpose; never present the app as a forecasting tool.

## Resources

| Path | Purpose |
| --- | --- |
| [references/diataxis.md](references/diataxis.md) | Diátaxis types and writing workflow |
| [references/document-placement.md](references/document-placement.md) | README vs `docs/` vs `docs-internal/` |
| [references/review-checklist.md](references/review-checklist.md) | Final review |
| [references/github-pages-docs.md](references/github-pages-docs.md) | Jekyll public docs |
| [references/readme-github.md](references/readme-github.md) | README rules |
| [assets/readme-section-template.md](assets/readme-section-template.md) | README sections |
| [assets/how-to-template.md](assets/how-to-template.md) | How-to guides |
| [assets/tutorial-template.md](assets/tutorial-template.md) | Tutorials |
| [assets/technical-blog-template.md](assets/technical-blog-template.md) | Technical blog posts |
