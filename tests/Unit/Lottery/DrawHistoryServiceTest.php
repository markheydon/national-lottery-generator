<?php

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use App\Game;
use App\Services\Lottery\DrawHistoryService;
use PHPUnit\Framework\TestCase;

class DrawHistoryServiceTest extends TestCase
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

    public function testEnsureAvailableUsesCachedCsvWhenStaleRefreshFails(): void
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

        $game = Game::findBySlug('lotto');
        $this->assertNotNull($game);
        $this->assertTrue(DrawHistoryService::ensureAvailable($game));
    }

    public function testEnsureAvailableFailsWhenNoCsvAndRefreshFails(): void
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

        $game = Game::findBySlug('lotto');
        $this->assertNotNull($game);
        $this->assertFalse(DrawHistoryService::ensureAvailable($game));

        seed_lottery_fixtures();
    }
}
