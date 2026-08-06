# Dependency Update Commands

This document outlines the commands and procedures for updating dependencies within the repository.

## Updating Dependencies

Typically, the following commands are run to update Composer dependencies and ensure everything is working correctly:

```bash
composer update
composer validate
composer audit
vendor/bin/phpunit
./vendor/bin/pint --test
```

There is no npm project for the application. Playwright is installed on demand in CI (and optionally locally) without a committed `package.json`.
