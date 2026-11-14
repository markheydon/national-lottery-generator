<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Bootstrap;

Bootstrap::loadEnv(dirname(__DIR__));

putenv('APP_ENV=testing');
$_ENV['APP_ENV'] = 'testing';

/**
 * Copy committed lottery CSV fixtures into storage for hermetic tests.
 */
function seed_lottery_fixtures(): void
{
    $fixtureDir = dirname(__DIR__) . '/tests/fixtures/lottery';
    $targetDir = storage_path('lottery');

    if (! is_dir($targetDir) && ! mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
        throw new RuntimeException('Failed to create lottery storage directory for tests');
    }

    foreach (glob($fixtureDir . '/*.csv') ?: [] as $fixturePath) {
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . basename($fixturePath);
        copy($fixturePath, $targetPath);
        touch($targetPath);
    }
}

seed_lottery_fixtures();
