<?php

/**
 * Helper class to download Set For Life draw history file.
 */

declare(strict_types=1);

namespace App\Services\Lottery;

/**
 * Helper class to download Set For Life draw history file.
 *
 * @package App\Services\Lottery
 * @since 1.0.0
 */
class SetForLifeDownload
{
    /** @var string URL of the draw history. */
    public const HISTORY_DOWNLOAD_URL = 'https://api-dfe.national-lottery.co.uk/draw-game/results/3/download?interval=ONE_EIGHTY';
    /** @var string Filename to use for the local (data directory) file. */
    public const FILENAME = 'set-for-life-draw-history';

    /**
     * Resolved download URL, allowing deterministic test overrides.
     *
     * @return string
     */
    private static function getHistoryDownloadUrl(): string
    {
        $override = getenv('LOTTERY_DOWNLOAD_URL_SET_FOR_LIFE');
        return ($override !== false && $override !== '') ? $override : self::HISTORY_DOWNLOAD_URL;
    }

    /**
     * Download the Set For Life draw history file.
     *
     * @since 1.0.0
     * @param bool $failDownload Simulate failed download (for testing).
     * @param bool $failRename Simulate failed rename of temp file (for testing).
     * @return string Error string on failure, otherwise empty string.
     */
    public static function download($failDownload = false, $failRename = false): string
    {
        $downloader = new Downloader(self::getHistoryDownloadUrl(), self::FILENAME);
        return $downloader->download($failDownload, $failRename);
    }

    /**
     * Uses the Set For Life draw history to return a draws array.
     *
     * @since 1.0.0
     *
     * @return array The draws array.
     */
    public static function readSetForLifeDrawHistory(): array
    {
        $results = Utils::csvToArray(self::filePath());

        $allDraws = [];
        foreach ($results as $draw) {
            $drawDate = $draw['DrawDate'];
            $ball1 = $draw['Ball 1'];
            $ball2 = $draw['Ball 2'];
            $ball3 = $draw['Ball 3'];
            $ball4 = $draw['Ball 4'];
            $ball5 = $draw['Ball 5'];
            $lifeBall = $draw['Life Ball'] ?? $draw['LifeBall'] ?? '';
            $ballSet = $draw['Ball Set'];
            $machine = $draw['Machine'];
            $drawNumber = $draw['DrawNumber'];
            $dayOfDraw = date('l', strtotime($drawDate));

            $allDraws[] = [
                'drawNumber' => $drawNumber,
                'drawDate' => $drawDate,
                'drawDay' => $dayOfDraw,
                'ball1' => $ball1,
                'ball2' => $ball2,
                'ball3' => $ball3,
                'ball4' => $ball4,
                'ball5' => $ball5,
                'lifeBall' => $lifeBall,
                'ballSet' => $ballSet,
                'machine' => $machine,
            ];
        }

        return $allDraws;
    }

    /**
     * Full path to the downloaded results file.
     *
     * @return string String containing the full path of the results file.
     */
    private static function filePath(): string
    {
        $downloader = new Downloader(self::getHistoryDownloadUrl(), self::FILENAME);
        return $downloader->filePath();
    }
}
