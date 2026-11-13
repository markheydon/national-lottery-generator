# Dependency Update Commands

This document outlines the commands and procedures for updating dependencies within the repository.

## Updating Dependencies

Typically, the following commands are run to update Composer dependencies and ensure everything is working correctly:

```bash
composer update
composer validate
composer audit
vendor/bin/phpunit
vendor/bin/pint --test
```

For Playwright E2E (`@playwright/test` in committed `package.json`):

- Apply updates via Dependabot PR or run `npm update` on a machine with Node.js, commit `package-lock.json`, and open a pull request.
- Validate Playwright in GitHub Actions CI (`.github/workflows/ci.yml` runs `npm run test:e2e`).

Local `npm run test:e2e` is not part of the standard contributor workflow.
