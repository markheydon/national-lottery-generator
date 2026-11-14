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

        $slug = $game->getSlug();
        $downloadRequired = CsvDownloadService::isDownloadRequired($downloader->filePath());
        $hasReadableDraws = GameGenerateRegistry::drawCount($slug) >= 1;

        if ($downloadRequired || ! $hasReadableDraws) {
            GameGenerateRegistry::refreshDownload($slug);
        }

        return GameGenerateRegistry::drawCount($slug) >= 1;
    }
}
