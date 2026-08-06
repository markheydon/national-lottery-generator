<?php

declare(strict_types=1);

namespace App\Http;

use App\Game;
use App\Services\Lottery\CsvDownloadService;
use App\Services\Lottery\EuromillionsDownload;
use App\Services\Lottery\EuromillionsGenerate;
use App\Services\Lottery\EuromillionsHotpicksGenerate;
use App\Services\Lottery\LottoDownload;
use App\Services\Lottery\LottoGenerate;
use App\Services\Lottery\LottoHotpicksGenerate;
use App\Services\Lottery\SetForLifeDownload;
use App\Services\Lottery\SetForLifeGenerate;
use App\Services\Lottery\ThunderballDownload;
use App\Services\Lottery\ThunderballGenerate;

class GameController
{
    public function index(): Response
    {
        return new Response(View::render('games/index', [
            'title' => 'Games',
            'games' => Game::all(),
        ]));
    }

    public function generate(string $slug): Response
    {
        $game = Game::findBySlug($slug);

        if (! $game) {
            return new Response('Game not found', 404);
        }

        $downloadRequired = $this->isDownloadRequiredForGame($game);

        $generate = match (strtolower($game->getGameName())) {
            'euromillions' => self::generateEuroMillions($downloadRequired),
            'lotto' => self::generateLotto($downloadRequired),
            'thunderball' => self::generateThunderball($downloadRequired),
            'lotto hotpicks' => self::generateLottoHotpicks($downloadRequired),
            'euromillions hotpicks' => self::generateEuroMillionsHotpicks($downloadRequired),
            'set for life' => self::generateSetForLife($downloadRequired),
            default => null,
        };

        if ($generate === null) {
            return new Response('Unsupported Game Name: ' . $game->getGameName(), 500);
        }

        $viewData = self::buildViewDataArray($generate);

        return new Response(View::render('games/generate', array_merge($viewData, [
            'gameLogo' => $game->getGameLogo(),
            'currentSlug' => $slug,
            'allGames' => Game::all(),
        ])));
    }

    private function isDownloadRequiredForGame(Game $game): bool
    {
        $downloader = $game->getDownloader();

        if (! $downloader) {
            return true;
        }

        return CsvDownloadService::isDownloadRequired($downloader->filePath());
    }

    /**
     * @return array<string, mixed>
     */
    private static function generateLotto(bool $downloadNeeded): array
    {
        if ($downloadNeeded) {
            LottoDownload::download();
        }

        return LottoGenerate::generate();
    }

    /**
     * @return array<string, mixed>
     */
    private static function generateEuroMillions(bool $downloadNeeded): array
    {
        if ($downloadNeeded) {
            EuromillionsDownload::download();
        }

        return EuromillionsGenerate::generate();
    }

    /**
     * @return array<string, mixed>
     */
    private static function generateThunderball(bool $downloadNeeded): array
    {
        if ($downloadNeeded) {
            ThunderballDownload::download();
        }

        return ThunderballGenerate::generate();
    }

    /**
     * @return array<string, mixed>
     */
    private static function generateLottoHotpicks(bool $downloadNeeded): array
    {
        if ($downloadNeeded) {
            LottoDownload::download();
        }

        return LottoHotpicksGenerate::generate();
    }

    /**
     * @return array<string, mixed>
     */
    private static function generateEuroMillionsHotpicks(bool $downloadNeeded): array
    {
        if ($downloadNeeded) {
            EuromillionsDownload::download();
        }

        return EuromillionsHotpicksGenerate::generate();
    }

    /**
     * @return array<string, mixed>
     */
    private static function generateSetForLife(bool $downloadNeeded): array
    {
        if ($downloadNeeded) {
            SetForLifeDownload::download();
        }

        return SetForLifeGenerate::generate();
    }

    /**
     * @param array<string, mixed> $generate
     * @return array<string, mixed>
     */
    private static function buildViewDataArray(array $generate): array
    {
        $suggested = [];
        $others = [];
        $lines = $generate['lines'];

        foreach ($lines as $method) {
            $suggested[] = array_shift($method);
            $others = array_merge($others, $method);
        }

        return [
            'title' => (string) $generate['gameName'],
            'gameName' => (string) $generate['gameName'],
            'latestDrawDate' => $generate['latestDrawDate']->format('l jS F'),
            'suggested' => self::formatLines($suggested),
            'others' => self::formatLines($others),
        ];
    }

    private static function formatNumbersLine(array $line): string
    {
        $output = '';

        while (($ball = array_shift($line)) !== null) {
            $output .= str_pad((string) $ball, 2, '0', STR_PAD_LEFT);

            if (count($line) > 0) {
                $output .= ' - ';
            }
        }

        return $output;
    }

    /**
     * @param array<int, array<string, mixed>> $lines
     * @return array<int, string>
     */
    private static function formatLines(array $lines): array
    {
        $result = [];

        foreach ($lines as $line) {
            $output = '';

            if (isset($line['mainNumbers'])) {
                $output .= self::formatNumbersLine($line['mainNumbers']);

                if (isset($line['luckyStars'])) {
                    $output .= ' ** ';
                    $output .= self::formatNumbersLine($line['luckyStars']);
                }

                if (isset($line['thunderball'])) {
                    $output .= ' ** ';
                    $output .= self::formatNumbersLine($line['thunderball']);
                }

                if (isset($line['lifeBall'])) {
                    $output .= ' ** ';
                    $output .= self::formatNumbersLine($line['lifeBall']);
                }
            } else {
                $output .= self::formatNumbersLine($line['lottoBalls']);
            }

            $result[] = $output;
        }

        return $result;
    }
}
