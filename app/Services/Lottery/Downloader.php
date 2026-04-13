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
     * Safely log a message if the Log facade is available.
     *
     * @param string $level Log level (debug, info, warning, error)
     * @param string $message Log message
     * @param array $context Additional context
     */
    private function safeLog(string $level, string $message, array $context = []): void
    {
        // Avoid noisy and non-deterministic logging during automated tests.
        if (\function_exists('app')) {
            try {
                if (app()->environment('testing')) {
                    return;
                }
            } catch (\Throwable $e) {
                // If the application container is not available, fall through and attempt logging.
            }
        }
        try {
            if (class_exists('\Illuminate\Support\Facades\Log')) {
                Log::$level($message, $context);
            }
        } catch (\Throwable $e) {
            // Silently fail if logging isn't available
        }
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
        if ($this->canUseLaravelStorage()) {
            return Storage::disk('local')->path($this->storagePath());
        }

        return $this->toAbsoluteLocalPath($this->storagePath());
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
        return $this->downloadWithStorage($failDownload, $failRename);
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
        if ($this->localFileExists($storagePath)) {
            $timestamp = date('YmdHis', time());
            $backupPath = self::STORAGE_PATH . DIRECTORY_SEPARATOR . $this->filename . '-' . $timestamp . '.csv';

            if (!$failRename) {
                try {
                    $this->copyLocalFile($storagePath, $backupPath);
                } catch (\Exception $e) {
                    $this->safeLog('error', 'Failed to backup old history file', [
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
            $this->writeLocalFile($storagePath, $response->body());

            return '';
        } catch (\Exception $e) {
            $this->safeLog('error', 'Failed to download lottery CSV', [
                'error' => $e->getMessage(),
                'url' => $this->url,
            ]);
            return 'Download failed';
        }
    }

    /**
     * Determine whether the Laravel storage binding can be safely used.
     */
    private function canUseLaravelStorage(): bool
    {
        if (!\function_exists('app')) {
            return false;
        }

        try {
            return app()->bound('filesystem');
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Convert local disk relative path to absolute storage/app path.
     */
    private function toAbsoluteLocalPath(string $relativePath): string
    {
        $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);
        $projectRoot = dirname(__DIR__, 3);

        return $projectRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . $normalizedPath;
    }

    /**
     * Check if file exists on the local disk.
     */
    private function localFileExists(string $relativePath): bool
    {
        if ($this->canUseLaravelStorage()) {
            return Storage::disk('local')->exists($relativePath);
        }

        return file_exists($this->toAbsoluteLocalPath($relativePath));
    }

    /**
     * Copy a local file, supporting Laravel Storage and native filesystem fallback.
     *
     * @throws \RuntimeException
     */
    private function copyLocalFile(string $fromRelativePath, string $toRelativePath): void
    {
        if ($this->canUseLaravelStorage()) {
            if (!Storage::disk('local')->copy($fromRelativePath, $toRelativePath)) {
                throw new \RuntimeException('Copy failed');
            }

            return;
        }

        $sourcePath = $this->toAbsoluteLocalPath($fromRelativePath);
        $destinationPath = $this->toAbsoluteLocalPath($toRelativePath);

        $destinationDir = dirname($destinationPath);
        if (!is_dir($destinationDir) && !mkdir($destinationDir, 0755, true) && !is_dir($destinationDir)) {
            throw new \RuntimeException('Failed to create destination directory');
        }

        if (!copy($sourcePath, $destinationPath)) {
            throw new \RuntimeException('Copy failed');
        }
    }

    /**
     * Write a local file, supporting Laravel Storage and native filesystem fallback.
     *
     * @throws \RuntimeException
     */
    private function writeLocalFile(string $relativePath, string $contents): void
    {
        if ($this->canUseLaravelStorage()) {
            if (!Storage::disk('local')->put($relativePath, $contents)) {
                throw new \RuntimeException('Write failed');
            }

            return;
        }

        $absolutePath = $this->toAbsoluteLocalPath($relativePath);
        $targetDir = dirname($absolutePath);

        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            throw new \RuntimeException('Failed to create target directory');
        }

        if (file_put_contents($absolutePath, $contents) === false) {
            throw new \RuntimeException('Write failed');
        }
    }

}
