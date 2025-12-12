<?php

/**
 * Helper class to download draw history files.
 */

declare(strict_types=1);

namespace App\Services\Lottery;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Helper class to download draw history files.
 *
 * Uses Laravel's Cache and Storage facades to implement cache-aside pattern.
 *
 * @package App\Services\Lottery
 * @since 1.0.0
 */
class Downloader
{
    /** @var string The storage subdirectory for lottery data. */
    private const STORAGE_PATH = 'lottery';

    /** @var string Legacy directory path for backward compatibility with tests. */
    private const LEGACY_DATA_PATH = __DIR__ . '/../../../storage/app/lottery-data';

    /** @var string Filename to use for successful download (excluding .csv). */
    private $filename;
    /** @var string URL to download from. */
    private $url;

    /**
     * Returns the storage path for the CSV file (without storage/app/).
     *
     * @since 1.0.0
     *
     * @return string Storage path of the download file.
     */
    public function storagePath(): string
    {
        return self::STORAGE_PATH . DIRECTORY_SEPARATOR . $this->filename . '.csv';
    }

    /**
     * Check if running in testing environment.
     *
     * @return bool
     */
    private function isTestingEnvironment(): bool
    {
        if (!function_exists('app')) {
            return false;
        }

        try {
            $app = app();
            // Check if it's a full Application instance (not just Container)
            if (method_exists($app, 'environment')) {
                return $app->environment('testing');
            }
        } catch (\Throwable $e) {
            // Log the error instead of silently catching it
            Log::warning('Exception during environment check in Downloader::isTestingEnvironment', [
                'error' => $e->getMessage(),
                'exception' => $e,
            ]);
        }

        // If unable to determine, assume not testing to avoid suppressing logs
        return false;
    }

    /**
     * Returns the full filepath of the download file.
     *
     * Including the .csv suffix.
     *
     * @since 1.0.0
     *
     * @return string Full path of the download file.
     */
    public function filePath(): string
    {
        // Check if we're in a Laravel application context
        if (function_exists('app') && app()->bound('filesystem')) {
            try {
                return Storage::disk('local')->path($this->storagePath());
            } catch (\RuntimeException $e) {
                // Fallback to legacy path if Storage disk is not configured
                if (!$this->isTestingEnvironment()) {
                    Log::debug('Storage facade not available, falling back to legacy path', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } else {
            // Log when falling back in non-Laravel context (likely unit tests)
            if (!$this->isTestingEnvironment()) {
                Log::warning('Filesystem not bound in application container, using legacy path');
            }
        }

        // Fallback to legacy path for unit tests or when Storage is not available
        return self::LEGACY_DATA_PATH . DIRECTORY_SEPARATOR . $this->filename . '.csv';
    }

    /**
     * Downloader constructor.
     *
     * @since 1.0.0
     *
     * @param string $url URL to use to download from.
     * @param string $filename Filename for local file excluding .csv extension.
     */
    public function __construct(string $url, string $filename)
    {
        $this->url = $url;
        $this->filename = $filename;
    }

    /**
     * Download the draw history file to storage.
     *
     * Uses Laravel's Storage facade to save files to storage/app/lottery/.
     * Falls back to legacy file operations for unit tests.
     *
     * @since 1.0.0
     *
     * @param bool $failDownload Simulate failed download (for testing).
     * @param bool $failRename Simulate failed renaming of temp file (for testing).
     * @return string Error string on failure, otherwise empty string.
     */
    public function download(bool $failDownload = false, bool $failRename = false): string
    {
        // Use Storage facade if available (Laravel app context), otherwise fall back to legacy
        if (function_exists('app') && app()->bound('filesystem')) {
            try {
                return $this->downloadWithStorage($failDownload, $failRename);
            } catch (\RuntimeException $e) {
                // Log the fallback in non-testing environments
                Log::warning('Storage facade failed, falling back to legacy download', [
                    'error' => $e->getMessage(),
                    'url' => $this->url,
                ]);
            } catch (\Exception $e) {
                // Catch other exceptions that might occur during Storage operations
                Log::error('Unexpected error during Storage download, falling back to legacy', [
                    'error' => $e->getMessage(),
                    'type' => get_class($e),
                    'url' => $this->url,
                ]);
            }
        }

        // Fallback to legacy file operations for unit tests or when Storage fails
        return $this->downloadLegacy($failDownload, $failRename);
    }

    /**
     * Download using Laravel Storage facade.
     *
     * @param bool $failDownload Simulate failed download (for testing).
     * @param bool $failRename Simulate failed renaming of temp file (for testing).
     * @return string Error string on failure, otherwise empty string.
     */
    private function downloadWithStorage(bool $failDownload, bool $failRename): string
    {
        $storagePath = $this->storagePath();

        // Create backup of existing file if it exists
        if (Storage::disk('local')->exists($storagePath)) {
            $timestamp = date('YmdHis', time());
            $backupPath = self::STORAGE_PATH . DIRECTORY_SEPARATOR . $this->filename . '-' . $timestamp . '.csv';

            if (!$failRename) {
                try {
                    Storage::disk('local')->copy($storagePath, $backupPath);
                } catch (\Exception $e) {
                    Log::error('Failed to backup old history file', [
                        'error' => $e->getMessage(),
                        'source' => $storagePath,
                        'destination' => $backupPath,
                    ]);
                    return 'Backup of old history file failed';
                }
            } else {
                return 'Renaming of old history file failed';
            }
        }

        // Download new file
        if ($failDownload) {
            return 'Download failed';
        }

        try {
            $timeout = (int) env('LOTTERY_DOWNLOAD_TIMEOUT', 30);
            $response = Http::timeout($timeout)->get($this->url);

            if (!$response->successful()) {
                return 'Download failed';
            }

            // Save to storage
            Storage::disk('local')->put($storagePath, $response->body());

            return '';
        } catch (\Exception $e) {
            Log::error('Failed to download lottery CSV', [
                'error' => $e->getMessage(),
                'url' => $this->url,
            ]);
            return 'Download failed';
        }
    }

    /**
     * Download using legacy file operations (for backward compatibility with tests).
     *
     * @param bool $failDownload Simulate failed download (for testing).
     * @param bool $failRename Simulate failed renaming of temp file (for testing).
     * @return string Error string on failure, otherwise empty string.
     */
    private function downloadLegacy(bool $failDownload, bool $failRename): string
    {
        // Ensure the data directory exists
        if (!file_exists(self::LEGACY_DATA_PATH)) {
            mkdir(self::LEGACY_DATA_PATH, 0755, true);
        }

        $filepath = self::LEGACY_DATA_PATH . DIRECTORY_SEPARATOR . $this->filename . '.csv';

        // determine a filename to rename the current file to (if there is one)
        $timestamp = date('YmdHis', time());
        $renameFilepath = (file_exists($filepath))
            ? self::LEGACY_DATA_PATH . DIRECTORY_SEPARATOR . $this->filename . '-' . $timestamp . '.csv' : '';

        // download new file
        // if it worked, then rename existing and replace with new
        // otherwise report failure
        $tempFilename = tempnam(sys_get_temp_dir(), 'lotto-draw-history');
        $downloadResult = $failDownload ? false : file_put_contents($tempFilename, fopen($this->url, 'r'));
        if (false === $downloadResult) {
            return 'Download failed';
        }
        if (strlen($renameFilepath) > 0) {
            $renameResult = $failRename ? false : rename($filepath, $renameFilepath);
            if (false === $renameResult) {
                return 'Renaming of old history file failed';
            }
        }
        $finalResult = rename($tempFilename, $filepath);
        return $finalResult ? '' : 'Renaming of newly download history file failed';
    }
}
