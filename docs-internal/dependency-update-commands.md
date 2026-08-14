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

For Playwright E2E (committed `package.json`):

```bash
npm update
npm audit
npm run test:e2e
```

After updating `@playwright/test`, run `npm run test:e2e:install` locally if browser binaries need refreshing.
