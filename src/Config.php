<?php

declare(strict_types=1);

namespace App;

/**
 * Application configuration loader.
 */
final class Config
{
    /**
     * @return array<int, array{slug: string, name: string, logo: string}>
     */
    public static function games(): array
    {
        /** @var array{games: array<int, array{slug: string, name: string, logo: string}>} $config */
        $config = require project_root() . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'games.php';

        return $config['games'];
    }
}
