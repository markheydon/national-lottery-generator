<?php

declare(strict_types=1);

namespace App\Http;

use App\Game;
use App\Services\Lottery\CsvDownloadService;
use App\Services\Lottery\DrawHistoryService;
use App\Services\Lottery\GameGenerateRegistry;

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

        if (! DrawHistoryService::ensureAvailable($game)) {
            return new Response(View::render('games/unavailable', [
                'title' => $game->getGameName(),
                'gameName' => $game->getGameName(),
                'gameLogo' => $game->getGameLogo(),
                'currentSlug' => $slug,
                'allGames' => Game::all(),
            ]), 503);
        }

        $slug = $game->getSlug();
        $downloadRequired = $this->isDownloadRequiredForGame($game)
            && GameGenerateRegistry::drawCount($slug) < 1;

        $generate = GameGenerateRegistry::generate($slug, $downloadRequired);

        if ($generate === null) {
            return new Response('Unsupported game: ' . $game->getSlug(), 500);
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
