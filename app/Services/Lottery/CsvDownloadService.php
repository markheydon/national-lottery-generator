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
     * Get the latest draw date from a CSV file.
     *
     * @param string $filepath Path to the CSV file
     * @return string|null Latest draw date or null if not found
     */
    private static function getLatestDrawDate(string $filepath): ?string
    {
        if (!file_exists($filepath)) {
            return null;
        }

        $csvData = Utils::csvToArray($filepath);
        if (empty($csvData)) {
            return null;
        }

        // CSV data is ordered with newest first
        $firstRow = $csvData[0];
        return $firstRow['DrawDate'] ?? null;
    }

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
        $dayInSeconds = 86400; // 24 * 60 * 60

        // Download if file is older than 1 day
        return ($currentTime - $fileTime) > $dayInSeconds;
    }

    /**
     * Get draw history with caching.
     *
     * Implements cache-aside pattern:
     * 1. Check cache
     * 2. If cache miss, check if file exists and is recent
     * 3. If file missing or old, download new CSV
     * 4. Parse CSV and cache the result
     *
     * @param string $gameName Game name for cache key
     * @param Downloader $downloader Downloader instance
     * @param callable $parseCallback Callback to parse CSV data, receives filepath as parameter
     * @return array Parsed draw history
     */
    public static function getDrawHistory(
        string $gameName,
        Downloader $downloader,
        callable $parseCallback
    ): array {
        $filepath = $downloader->filePath();
        $storagePath = $downloader->storagePath();

        // Get latest draw date for cache key (or use 'none' if file doesn't exist)
        $drawId = 'none';
        if (file_exists($filepath)) {
            $latestDrawDate = self::getLatestDrawDate($filepath);
            if ($latestDrawDate) {
                $drawId = str_replace('-', '', $latestDrawDate); // e.g., "20231225"
            }
        }

        $cacheKey = "lottery:csv:{$gameName}:{$drawId}";

        // Try to get from cache first
        $data = Cache::remember($cacheKey, now()->addDays(7), function () use (
            $downloader,
            $filepath,
            $storagePath,
            $parseCallback
        ) {
            // Check if download is needed
            if (self::isDownloadRequired($filepath)) {
                $downloader->download();
            }

            // Parse the CSV file
            $parsed = $parseCallback($filepath);

            // Optionally save parsed JSON for debugging/inspection
            $jsonPath = str_replace('.csv', '.json', $storagePath);
            try {
                Storage::disk('local')->put(
                    $jsonPath,
                    json_encode($parsed, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
                );
            } catch (\Exception $e) {
                // Silently fail - JSON storage is optional
            }

            return $parsed;
        });

        return $data;
    }
}
