<?php

namespace App\Http\Controllers;

use App\Game;
use MarkHeydon\LotteryGenerator\EuromillionsDownload;
use MarkHeydon\LotteryGenerator\EuromillionsGenerate;
use MarkHeydon\LotteryGenerator\EuromillionsHotpicksGenerate;
use MarkHeydon\LotteryGenerator\LottoDownload;
use MarkHeydon\LotteryGenerator\LottoGenerate;
use MarkHeydon\LotteryGenerator\LottoHotpicksGenerate;
use MarkHeydon\LotteryGenerator\ThunderballDownload;
use MarkHeydon\LotteryGenerator\ThunderballGenerate;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $games = Game::all();

        return view(
            'games.index',
            [
                'title' => 'Games',
                'games' => $games,
            ]
        );
    }

    /**
     * Displays generated number for the specified Game.
     *
     * @param Game $game Game to generate numbers for.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception new \DateTime could fail in theory but shouldn't.
     */
    public function generate(Game $game)
    {
        // Deal with history download required (or not).
        $downloadRequired = self::isDownloadRequired($game->getLastHistoryDownload());

        // Rubbish way to check which generate to call, but will do for now.
        $generate = [];
        switch (strtolower($game->getGameName())) {
            case 'euromillions':
                $generate = self::generateEuroMillions($downloadRequired);
                break;
            case 'lotto':
                $generate = self::generateLotto($downloadRequired);
                break;
            case 'thunderball':
                $generate = self::generateThunderball($downloadRequired);
                break;
            case 'lotto hotpicks':
                $generate = self::generateLottoHotpicks($downloadRequired);
                break;
            case 'euromillions hotpicks':
                $generate = self::generateEuroMillionsHotpicks($downloadRequired);
                break;
            default:
                dd('Unsupported Game Name: ' . $game->getGameName());
        }

        // Private generate...() method will have downloaded, so update the last_history_download datetime.
        if ($downloadRequired) {
            $game->last_history_download = new \DateTime();
            $game->save();
        }

        // Build the stuff we are interested in for the view.
        $viewData = self::buildViewDataArray($generate);
        return view(
            'games.generate',
            array_merge($viewData, ['gameLogo' => $game->getGameLogo()])
        );
    }

    /**
     * Decide if new download required based on game history download date.
     *
     * @param \DateTime $lastDownloaded of last history download.
     *
     * @return bool True is download needed; false otherwise.
     * @throws \Exception new \DateTime could fail in theory but shouldn't.
     */
    private static function isDownloadRequired(\DateTime $lastDownloaded): bool
    {
        $dateDiff = $lastDownloaded->diff(new \DateTime());
        $result = ($dateDiff->d > 0);

        return $result;
    }

    /**
     * Generate Lotto numbers array.
     *
     * @param bool $downloadNeeded True if need to download new history file first.
     *
     * @return array
     */
    private static function generateLotto(bool $downloadNeeded): array
    {
        if ($downloadNeeded) {
            LottoDownload::download();
        }
        $generate = LottoGenerate::generate();

        return $generate;
    }

    /**
     * Generate EuroMillions numbers array.
     *
     * @param bool $downloadNeeded True if need to download new history file first.
     *
     * @return array
     */
    private static function generateEuroMillions(bool $downloadNeeded): array
    {
        if ($downloadNeeded) {
            EuromillionsDownload::download();
        }
        $generate = EuromillionsGenerate::generate();

        return $generate;
    }

    /**
     * Generate Thunderball numbers array.
     *
     * @param bool $downloadNeeded True if need to download new history file first.
     *
     * @return array
     */
    private static function generateThunderball(bool $downloadNeeded): array
    {
        if ($downloadNeeded) {
            ThunderballDownload::download();
        }
        $generate = ThunderballGenerate::generate();

        return $generate;
    }

    /**
     * Generate Lotto Hotpicks numbers array.
     *
     * @param bool $downloadNeeded True if need to download new history file first.
     *
     * @return array
     */
    private static function generateLottoHotpicks(bool $downloadNeeded): array
    {
        if ($downloadNeeded) {
            LottoDownload::download();
        }
        $generate = LottoHotpicksGenerate::generate();

        return $generate;
    }

    /**
     * Generate EuroMillions Hotpicks numbers array.
     *
     * @param bool $downloadNeeded True if need to download new history file first.
     *
     * @return array
     */
    private static function generateEuromillionsHotpicks(bool $downloadNeeded): array
    {
        if ($downloadNeeded) {
            EuroMillionsDownload::download();
        }
        $generate = EuroMillionsHotpicksGenerate::generate();

        return $generate;
    }

    /**
     * Build view data array.
     *
     * I.e. take the $generate array from the generate() method and make it output friendly.
     *
     * @param array $generate
     * @return array
     */
    private static function buildViewDataArray(array $generate): array
    {
        // Deal with the generated lines.  Use 1st from each method as 'suggested',
        // the rest goes in 'others'.
        $suggested = [];
        $others = [];
        $lines = $generate['lines'];
        foreach ($lines as $method) {
            $suggested[] = array_shift($method);
            $others = array_merge($others, $method);
        }

        $data = [
            'title' => (string)$generate['gameName'],
            'gameName' => (string)$generate['gameName'],
            'latestDrawDate' => $generate['latestDrawDate']->format('l jS F'),
            'suggested' => self::formatLines($suggested),
            'others' => self::formatLines($others),
        ];
        return $data;
    }

    /**
     * Format a single line of numbers.
     *
     * Basically puts ' - ' inbetween the numbers.
     *
     * @param array $line Line to format.
     * @return string Numbers formatted.
     */
    private static function formatNumbersLine(array $line): string
    {
        $output = '';
        while (($ball = array_shift($line)) !== null) {
            $output .= str_pad($ball, 2, '0', STR_PAD_LEFT);
            if (count($line) > 0) {
                $output .= ' - ';
            }
        }
        $output .= implode(' - ', $line);
        return $output;
    }

    /**
     * Format the numbers from the array of passed in lines.
     *
     * @param array $lines Lines to output.
     * @return array Array of formatted lines.
     */
    private static function formatLines(array $lines): array
    {
        $result = [];
        foreach ($lines as $line) {
            $output = '';
            if (isset($line['mainNumbers'])) {
                $aLine = $line['mainNumbers'];
                $output .= self::formatNumbersLine($aLine);

                if (isset($line['luckyStars'])) {
                    $output .= ' ** ';
                    $aLine = $line['luckyStars'];
                    $output .= self::formatNumbersLine($aLine);
                }

                if (isset($line['thunderball'])) {
                    $output .= ' ** ';
                    $aLine = $line['thunderball'];
                    $output .= self::formatNumbersLine($aLine);
                }
            } else {
                $aLine = $line['lottoBalls'];
                $output .= self::formatNumbersLine($aLine);
            }
            $result[] = $output;
        }
        return $result;
    }
}
