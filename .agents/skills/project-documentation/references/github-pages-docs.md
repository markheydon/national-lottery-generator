# GitHub Pages documentation (`docs/`)

Public user documentation lives in `docs/`. See [AGENTS.md](../../../AGENTS.md) for how this app is published and the entertainment-only constraint.

## Jekyll

- Site config: `docs/_config.yml`
- Custom domain when used: `docs/CNAME`
- Compatible with GitHub Pages; use relative links within `docs/`

## Page roles

| Page | Typical Diátaxis role |
| --- | --- |
| `index.md` | Orientation / start here |
| `getting-started.md` | Tutorial or how-to |
| `how-it-works.md` | Explanation |
| `faq.md` | How-to / reference (short answers) |
| `developer-reference.md` | Reference (when present) |

Keep **one primary purpose** per page unless structure clearly separates types.

## Audience and tone

End users of the National Lottery Number Generator web app.

- Friendly, practical, clear next steps
- Avoid implementation detail unless it directly helps users
- Preserve entertainment-only disclaimer and responsible gambling messaging
- Do not name documentation frameworks in body prose

## Before editing

Inspect behaviour and UI against:

- `config/games.php`
- `public/index.php`
- `templates/games/`
- Existing `docs/*.md` pages

Make minimal, high-signal edits. Add a new page only when there is clear evidence-based value.
