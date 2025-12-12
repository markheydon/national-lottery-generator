<?php

/**
 * Service for managing CSV downloads with caching.
 */

declare(strict_types=1);

namespace App\Services\Lottery;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * Service for managing CSV downloads with caching.
 *
 * Implements cache-aside pattern: checks cache first, then file storage,
 * then downloads if needed.
 *
 * @package App\Services\Lottery
 */
class CsvDownloadService
{
    /**
     * Check if download is required based on file timestamp and latest draw.
     *
     * Downloads are needed if:
     * - File doesn't exist
     * - File is older than 1 day
     *
     * @param string $filepath Path to the CSV file
     * @return bool True if download is needed
     */
    public static function isDownloadRequired(string $filepath): bool
    {
        if (!file_exists($filepath)) {
            return true;
        }

        // Check file modification time
        $fileTime = filemtime($filepath);
        $currentTime = time();

        // Download if file is older than 1 day (24 * 60 * 60 seconds)
        return ($currentTime - $fileTime) > (24 * 60 * 60);
    }

}
