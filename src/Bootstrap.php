<?php

declare(strict_types=1);

namespace App;

use Dotenv\Dotenv;

/**
 * Application bootstrap: environment loading and test detection.
 */
final class Bootstrap
{
    private static bool $envLoaded = false;

    /**
     * Load environment variables from .env when present.
     */
    public static function loadEnv(?string $projectRoot = null): void
    {
        if (self::$envLoaded) {
            return;
        }

        $root = $projectRoot ?? dirname(__DIR__);
        $envFile = $root . DIRECTORY_SEPARATOR . '.env';

        if (is_file($envFile)) {
            Dotenv::createImmutable($root)->safeLoad();
        }

        self::$envLoaded = true;
    }

    /**
     * Whether the application is running under PHPUnit.
     */
    public static function isTesting(): bool
    {
        return getenv('APP_ENV') === 'testing'
            || (defined('PHPUNIT_COMPOSER_INSTALL') && PHPUNIT_COMPOSER_INSTALL !== '');
    }
}
