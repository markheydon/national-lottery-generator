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
            $error = self::downloadForSlug($game->getSlug());

            if ($error !== '') {
                return false;
            }
        }

        return self::drawCountForSlug($game->getSlug()) >= 1;
    }

    private static function downloadForSlug(string $slug): string
    {
        return match ($slug) {
            'lotto', 'lotto-hotpicks' => LottoDownload::download(),
            'euromillions', 'euromillions-hotpicks' => EuromillionsDownload::download(),
            'thunderball' => ThunderballDownload::download(),
            'set-for-life' => SetForLifeDownload::download(),
            default => 'Unsupported game',
        };
    }

    private static function drawCountForSlug(string $slug): int
    {
        $draws = match ($slug) {
            'lotto', 'lotto-hotpicks' => LottoDownload::readLottoDrawHistory(),
            'euromillions', 'euromillions-hotpicks' => EuromillionsDownload::readEuromillionsDrawHistory(),
            'thunderball' => ThunderballDownload::readThunderballDrawHistory(),
            'set-for-life' => SetForLifeDownload::readSetForLifeDrawHistory(),
            default => [],
        };

        return count($draws);
    }
}
