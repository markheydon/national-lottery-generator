<?php

declare(strict_types=1);

namespace App\Services\Lottery;

use App\Game;

/**
 * Ensures draw-history CSV files are present and readable before generation.
 */
final class DrawHistoryService
{
    public static function ensureAvailable(Game $game): bool
    {
        $downloader = $game->getDownloader();

        if ($downloader === null) {
            return false;
        }

        if (CsvDownloadService::isDownloadRequired($downloader->filePath())) {
            $error = GameGenerateRegistry::refreshDownload($game->getSlug());

            if ($error !== '') {
                return false;
            }
        }

        return GameGenerateRegistry::drawCount($game->getSlug()) >= 1;
    }
}
