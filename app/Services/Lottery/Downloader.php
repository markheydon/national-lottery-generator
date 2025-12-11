<?php

/**
 * Helper class to download draw history files.
 */

declare(strict_types=1);

namespace App\Services\Lottery;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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
        return self::STORAGE_PATH . '/' . $this->filename . '.csv';
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
        return Storage::disk('local')->path($this->storagePath());
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
     *
     * @since 1.0.0
     *
     * @param bool $failDownload Simulate failed download (for testing).
     * @param bool $failRename Simulate failed renaming of temp file (for testing).
     * @return string Error string on failure, otherwise empty string.
     */
    public function download(bool $failDownload = false, bool $failRename = false): string
    {
        $storagePath = $this->storagePath();
        
        // Create backup of existing file if it exists
        if (Storage::disk('local')->exists($storagePath)) {
            $timestamp = date('YmdHis', time());
            $backupPath = self::STORAGE_PATH . '/' . $this->filename . '-' . $timestamp . '.csv';
            
            if (!$failRename) {
                try {
                    Storage::disk('local')->copy($storagePath, $backupPath);
                } catch (\Exception $e) {
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
            $response = Http::timeout(30)->get($this->url);
            
            if (!$response->successful()) {
                return 'Download failed';
            }
            
            // Save to storage
            Storage::disk('local')->put($storagePath, $response->body());
            
            return '';
        } catch (\Exception $e) {
            return 'Download failed';
        }
    }
}
