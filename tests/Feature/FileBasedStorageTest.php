<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Game;
use App\Http\Application;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FileBasedStorageTest extends TestCase
{
    public function test_games_load_from_config(): void
    {
        $games = Game::all();

        $this->assertIsArray($games);
        $this->assertGreaterThan(0, count($games));

        $lottoGame = Game::findBySlug('lotto');
        $this->assertNotNull($lottoGame);
        $this->assertSame('Lotto', $lottoGame->getGameName());
    }

    public function test_game_can_create_downloader(): void
    {
        $game = Game::findBySlug('lotto');
        $this->assertNotNull($game);

        $downloader = $game->getDownloader();
        $this->assertNotNull($downloader);
        $this->assertInstanceOf(\App\Services\Lottery\Downloader::class, $downloader);
    }

    public function test_storage_directory_accessible(): void
    {
        $lotteryDir = storage_path('lottery');

        if (! is_dir($lotteryDir)) {
            mkdir($lotteryDir, 0755, true);
        }

        $this->assertDirectoryExists($lotteryDir);
        $this->assertTrue(is_writable($lotteryDir));
    }

    public function test_homepage_loads(): void
    {
        $response = (new Application())->handle('GET', '/');

        $this->assertSame(200, $response->getStatus());
        $this->assertStringContainsString('Lottery Generator', $response->getBody());
        $this->assertStringContainsString('Lotto', $response->getBody());
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function gameSlugProvider(): array
    {
        return [
            'lotto' => ['lotto'],
            'euromillions' => ['euromillions'],
            'thunderball' => ['thunderball'],
            'set-for-life' => ['set-for-life'],
            'lotto-hotpicks' => ['lotto-hotpicks'],
            'euromillions-hotpicks' => ['euromillions-hotpicks'],
        ];
    }

    #[DataProvider('gameSlugProvider')]
    public function test_game_generate_route_returns_200(string $slug): void
    {
        $response = (new Application())->handle('GET', '/game/' . $slug . '/generate');

        $this->assertSame(200, $response->getStatus());
        $this->assertStringContainsString('Suggested Lines', $response->getBody());
        $this->assertStringContainsString(
            'While the numbers generated using this just-for-fun tool are not random',
            $response->getBody()
        );
    }

    public function test_unknown_slug_returns_404(): void
    {
        $response = (new Application())->handle('GET', '/game/not-a-real-game/generate');

        $this->assertSame(404, $response->getStatus());
    }
}
