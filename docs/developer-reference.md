# Developer Reference

This page is a reference for maintaining public documentation in this repository.

## Purpose

The `docs/` folder contains GitHub Pages content aimed at end users. Keep pages concise and focused on one purpose.

- **Tutorial/start page**: [index.md](index.md)
- **How-to task guide**: [getting-started.md](getting-started.md)
- **Explanation**: [how-it-works.md](how-it-works.md)
- **FAQ/troubleshooting**: [faq.md](faq.md)

## Source of Truth Files

When updating public docs, align wording with:

- [composer.json](../composer.json)
- [config/games.php](../config/games.php)
- [routes/web.php](../routes/web.php)
- [app/Http/Controllers/GameController.php](../app/Http/Controllers/GameController.php)
- [resources/views/games/index.blade.php](../resources/views/games/index.blade.php)
- [resources/views/games/generate.blade.php](../resources/views/games/generate.blade.php)
- [README.md](../README.md)
- [CONTRIBUTING.md](../CONTRIBUTING.md)

## Organisation

Current public docs structure:

```
docs/
├── index.md               (start-here page)
├── getting-started.md     (how-to)
├── how-it-works.md        (explanation)
├── faq.md                 (reference/FAQ)
└── developer-reference.md (this file)
```

## Contributing to Documentation

Documentation contributions are welcome. When editing docs:

1. Use UK English and plain language.
2. Keep claims evidence-based and repository-native.
3. Use relative links for in-repository pages.
4. Keep one clear purpose per page.
5. Avoid mixing developer setup into end-user pages.

## Quick Links

- [Main README](../README.md) - Project overview and quick start
- [Contributing Guidelines](../CONTRIBUTING.md) - How to contribute to the project
- [Licence](../LICENSE) - Project licence information

## Questions or Feedback?

If you have questions about the documentation or suggestions for improvements, please open an issue on the GitHub repository.
