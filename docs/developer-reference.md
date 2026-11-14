# Developer Reference

This page is a reference for maintaining documentation in this repository.

## Purpose

| Location | Audience |
|----------|----------|
| `website/content/` | End users (published at [lottery.markheydon.me.uk](https://lottery.markheydon.me.uk/)) |
| `docs/` | Maintainers and coding agents |

Keep public pages concise and focused on one purpose.

Public guide sources:

- **Home**: `website/content/_index.md`
- **Getting started**: `website/content/guides/getting-started.md`
- **Explanation**: `website/content/guides/how-it-works.md`
- **FAQ**: `website/content/guides/faq.md`

## Source of Truth Files

When updating public docs, align wording with:

- [composer.json](../composer.json)
- [config/games.php](../config/games.php)
- [public/index.php](../public/index.php)
- [src/Http/GameController.php](../src/Http/GameController.php)
- [src/Services/Lottery/GameGenerateRegistry.php](../src/Services/Lottery/GameGenerateRegistry.php)
- [src/Services/Lottery/DrawHistoryService.php](../src/Services/Lottery/DrawHistoryService.php)
- [templates/games/index.php](../templates/games/index.php)
- [templates/games/generate.php](../templates/games/generate.php)
- [templates/games/unavailable.php](../templates/games/unavailable.php)
- [deploy/README.md](../deploy/README.md) — local Apache/nginx and Azure notes
- [deploy/nginx-default](../deploy/nginx-default) (nginx helper for Azure or local)
- [README.md](../README.md)
- [CONTRIBUTING.md](../CONTRIBUTING.md)

## Organisation

```
website/content/
├── _index.md
└── guides/
    ├── _index.md
    ├── getting-started.md
    ├── how-it-works.md
    └── faq.md

docs/
├── README.md
├── development-setup.md
├── supported-versions.md
├── dependency-update-commands.md
├── documentation-site.md
└── developer-reference.md (this file)
```

## Contributing to Documentation

Documentation contributions are welcome. When editing user-facing pages:

1. Use UK English and plain language.
2. Keep claims evidence-based and repository-native.
3. Use site-relative links in Hugo content (for example `/guides/faq/`).
4. Keep one clear purpose per page.
5. Avoid mixing developer setup into end-user pages.

For Hugo authoring conventions, see [documentation-site.md](documentation-site.md).

## Quick Links

- [Main README](../README.md) — project overview and quick start
- [Contributing guidelines](../CONTRIBUTING.md)
- [Licence](../LICENSE)

## Questions or Feedback?

Open an issue on the GitHub repository.