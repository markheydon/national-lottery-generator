<?php

/**
 * Service for managing CSV downloads with caching.
 */

declare(strict_types=1);

namespace App\Services\Lottery;

/**
 * Service for managing CSV downloads with caching.
 *
 * @package App\Services\Lottery
 */
class CsvDownloadService
{
    /**
     * Check if download is required based on file timestamp.
     *
     * Downloads are needed if the file doesn't exist or is older than 24 hours.
     */
    public static function isDownloadRequired(string $filepath): bool
    {
        if (! file_exists($filepath)) {
            return true;
        }

        $fileTime = filemtime($filepath);
        $currentTime = time();

        return ($currentTime - $fileTime) > (24 * 60 * 60);
    }
}
