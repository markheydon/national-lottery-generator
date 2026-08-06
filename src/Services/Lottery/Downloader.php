<?php

/**
 * Helper class to download draw history files.
 */

declare(strict_types=1);

namespace App\Services\Lottery;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Helper class to download draw history files.
 *
 * @package App\Services\Lottery
 * @since 1.0.0
 */
class Downloader
{
    /** @var string The storage subdirectory for lottery data. */
    private const STORAGE_PATH = 'lottery';

    /** @var string Filename to use for successful download (excluding .csv). */
    private string $filename;

    /** @var string URL to download from. */
    private string $url;

    /**
     * Returns the storage path for the CSV file (without storage/app/).
     */
    public function storagePath(): string
    {
        return self::STORAGE_PATH . DIRECTORY_SEPARATOR . $this->filename . '.csv';
    }

    /**
     * Returns the full filepath of the download file.
     *
     * Including the .csv suffix.
     */
    public function filePath(): string
    {
        return $this->toAbsoluteLocalPath($this->storagePath());
    }

    /**
     * Downloader constructor.
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
     * @param bool $failDownload Simulate failed download (for testing).
     * @param bool $failRename Simulate failed renaming of temp file (for testing).
     * @return string Error string on failure, otherwise empty string.
     */
    public function download(bool $failDownload = false, bool $failRename = false): string
    {
        $storagePath = $this->storagePath();

        if ($this->localFileExists($storagePath)) {
            $timestamp = date('YmdHis', time());
            $backupPath = self::STORAGE_PATH . DIRECTORY_SEPARATOR . $this->filename . '-' . $timestamp . '.csv';

            if (! $failRename) {
                try {
                    $this->copyLocalFile($storagePath, $backupPath);
                } catch (\Exception $e) {
                    $this->safeLog('Failed to backup old history file: ' . $e->getMessage());

                    return 'Backup of old history file failed';
                }
            } else {
                return 'Renaming of old history file failed';
            }
        }

        if ($failDownload) {
            return 'Download failed';
        }

        try {
            $timeout = (int) env('LOTTERY_DOWNLOAD_TIMEOUT', 30);
            $client = new Client(['timeout' => $timeout]);
            $response = $client->request('GET', $this->url);

            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                return 'Download failed';
            }

            $this->writeLocalFile($storagePath, (string) $response->getBody());

            return '';
        } catch (GuzzleException|\Exception $e) {
            $this->safeLog('Failed to download lottery CSV: ' . $e->getMessage());

            return 'Download failed';
        }
    }

    /**
     * Log a message unless running in tests.
     */
    private function safeLog(string $message): void
    {
        if (\App\Bootstrap::isTesting()) {
            return;
        }

        error_log($message);
    }

    /**
     * Convert local disk relative path to absolute storage/app path.
     */
    private function toAbsoluteLocalPath(string $relativePath): string
    {
        return storage_path($relativePath);
    }

    /**
     * Check if file exists on the local disk.
     */
    private function localFileExists(string $relativePath): bool
    {
        return file_exists($this->toAbsoluteLocalPath($relativePath));
    }

    /**
     * Copy a local file within storage/app/.
     *
     * @throws \RuntimeException
     */
    private function copyLocalFile(string $fromRelativePath, string $toRelativePath): void
    {
        $sourcePath = $this->toAbsoluteLocalPath($fromRelativePath);
        $destinationPath = $this->toAbsoluteLocalPath($toRelativePath);

        $destinationDir = dirname($destinationPath);
        if (! is_dir($destinationDir) && ! mkdir($destinationDir, 0755, true) && ! is_dir($destinationDir)) {
            throw new \RuntimeException('Failed to create destination directory');
        }

        if (! copy($sourcePath, $destinationPath)) {
            throw new \RuntimeException('Copy failed');
        }
    }

    /**
     * Write a local file within storage/app/.
     *
     * @throws \RuntimeException
     */
    private function writeLocalFile(string $relativePath, string $contents): void
    {
        $absolutePath = $this->toAbsoluteLocalPath($relativePath);
        $targetDir = dirname($absolutePath);

        if (! is_dir($targetDir) && ! mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            throw new \RuntimeException('Failed to create target directory');
        }

        if (file_put_contents($absolutePath, $contents) === false) {
            throw new \RuntimeException('Write failed');
        }
    }
}
