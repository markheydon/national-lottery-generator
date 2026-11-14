<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Game;
use App\Http\Application;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FileBasedStorageTest extends TestCase
{
    private ?string $savedLottoDownloadUrl = null;

    private ?string $savedDownloadTimeout = null;

    protected function tearDown(): void
    {
        if ($this->savedLottoDownloadUrl !== null) {
            putenv('LOTTERY_DOWNLOAD_URL_LOTTO=' . $this->savedLottoDownloadUrl);
            $_ENV['LOTTERY_DOWNLOAD_URL_LOTTO'] = $this->savedLottoDownloadUrl;
        } else {
            putenv('LOTTERY_DOWNLOAD_URL_LOTTO');
            unset($_ENV['LOTTERY_DOWNLOAD_URL_LOTTO']);
        }

        if ($this->savedDownloadTimeout !== null) {
            putenv('LOTTERY_DOWNLOAD_TIMEOUT=' . $this->savedDownloadTimeout);
            $_ENV['LOTTERY_DOWNLOAD_TIMEOUT'] = $this->savedDownloadTimeout;
        } else {
            putenv('LOTTERY_DOWNLOAD_TIMEOUT');
            unset($_ENV['LOTTERY_DOWNLOAD_TIMEOUT']);
        }

        seed_lottery_fixtures();

        parent::tearDown();
    }

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

    public function test_generate_returns_503_when_draw_history_unavailable(): void
    {
        $historyPath = storage_path('lottery/lotto-draw-history.csv');

        if (is_file($historyPath)) {
            unlink($historyPath);
        }

        $this->savedLottoDownloadUrl = getenv('LOTTERY_DOWNLOAD_URL_LOTTO') !== false
            ? (string) getenv('LOTTERY_DOWNLOAD_URL_LOTTO')
            : '';
        $this->savedDownloadTimeout = getenv('LOTTERY_DOWNLOAD_TIMEOUT') !== false
            ? (string) getenv('LOTTERY_DOWNLOAD_TIMEOUT')
            : '';

        putenv('LOTTERY_DOWNLOAD_URL_LOTTO=http://127.0.0.1:9/unreachable');
        $_ENV['LOTTERY_DOWNLOAD_URL_LOTTO'] = 'http://127.0.0.1:9/unreachable';
        putenv('LOTTERY_DOWNLOAD_TIMEOUT=1');
        $_ENV['LOTTERY_DOWNLOAD_TIMEOUT'] = '1';

        $response = (new Application())->handle('GET', '/game/lotto/generate');

        $this->assertSame(503, $response->getStatus());
        $this->assertStringContainsString('Draw history is not available', $response->getBody());
        $this->assertStringContainsString(
            'While the numbers generated using this just-for-fun tool are not random',
            $response->getBody()
        );

        seed_lottery_fixtures();
    }

    public function test_generate_serves_stale_cache_when_refresh_fails(): void
    {
        $historyPath = storage_path('lottery/lotto-draw-history.csv');
        $this->assertFileExists($historyPath);

        touch($historyPath, time() - (25 * 60 * 60));

        $this->savedLottoDownloadUrl = getenv('LOTTERY_DOWNLOAD_URL_LOTTO') !== false
            ? (string) getenv('LOTTERY_DOWNLOAD_URL_LOTTO')
            : '';
        $this->savedDownloadTimeout = getenv('LOTTERY_DOWNLOAD_TIMEOUT') !== false
            ? (string) getenv('LOTTERY_DOWNLOAD_TIMEOUT')
            : '';

        putenv('LOTTERY_DOWNLOAD_URL_LOTTO=http://127.0.0.1:9/unreachable');
        $_ENV['LOTTERY_DOWNLOAD_URL_LOTTO'] = 'http://127.0.0.1:9/unreachable';
        putenv('LOTTERY_DOWNLOAD_TIMEOUT=1');
        $_ENV['LOTTERY_DOWNLOAD_TIMEOUT'] = '1';

        $response = (new Application())->handle('GET', '/game/lotto/generate');

        $this->assertSame(200, $response->getStatus());
        $this->assertStringContainsString('Suggested Lines', $response->getBody());
    }

    public function test_non_get_method_returns_405_with_allow_header(): void
    {
        $response = (new Application())->handle('POST', '/');

        $this->assertSame(405, $response->getStatus());
        $this->assertSame('GET', $response->getHeaders()['Allow'] ?? null);
    }
}
