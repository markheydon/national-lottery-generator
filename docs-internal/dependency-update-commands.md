# Dependency Update Commands

This document outlines the commands and procedures for updating dependencies within the repository.

## Updating Dependencies

Start the containers:

```
./vendor/bin/sail up -d
```

Typically, the following commands are run to update dependencies and ensure everything is working correctly:

```
./vendor/bin/sail composer update
./vendor/bin/sail yarn upgrade
./vendor/bin/sail artisan test
./vendor/bin/sail pint --test
./vendor/bin/sail composer validate
./vendor/bin/sail composer audit
```
