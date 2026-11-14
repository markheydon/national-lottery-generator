# Hugo documentation site

Public user documentation lives in `website/content/`. See [AGENTS.md](../../../AGENTS.md) for site URL, theme, and deploy behaviour.

## Front matter

```yaml
---
title: Page title
weight: 10
aliases:
  - /legacy-path.html
---
```

Use `weight` to control sidebar order within a section. Add `aliases` when replacing old Jekyll URLs.

## Page roles

| Content | Typical role |
| --- | --- |
| `content/_index.md` | Orientation / start here |
| `content/guides/getting-started.md` | Tutorial or how-to |
| `content/guides/how-it-works.md` | Explanation |
| `content/guides/faq.md` | Reference (short answers) |

Keep **one primary purpose** per page unless structure clearly separates types.

## Audience and tone

End users of the National Lottery Number Generator web app.

- Friendly, practical, clear next steps
- Avoid implementation detail unless it directly helps users
- Preserve entertainment-only disclaimer and responsible gambling messaging
- Do not name documentation frameworks in body prose

Use site-relative links (for example `/guides/faq/`).

## Before editing

Inspect behaviour and UI against:

- `config/games.php`
- `public/index.php`
- `templates/games/`
- Existing `website/content/` pages

Make minimal, high-signal edits. Add a new page only when there is clear evidence-based value.

## Validation

From the repository root:

```shell
./scripts/invoke-hugo-site.sh build
./scripts/invoke-hugo-site.sh preview
./scripts/invoke-hugo-site.sh serve
```

PowerShell: `scripts/Invoke-HugoSite.ps1` with the same commands.