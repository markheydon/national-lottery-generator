<?php

declare(strict_types=1);

namespace Tests\Unit\Lottery;

use App\Config;
use App\Services\Lottery\GameGenerateRegistry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GameGenerateRegistryTest extends TestCase
{
    public function testSupportsAllConfiguredGames(): void
    {
        foreach (Config::games() as $game) {
            $this->assertTrue(
                GameGenerateRegistry::supports($game['slug']),
                'Missing registry entry for slug: ' . $game['slug']
            );
        }
    }

    public function testSupportsReturnsFalseForUnknownSlug(): void
    {
        $this->assertFalse(GameGenerateRegistry::supports('not-a-game'));
    }

    public function testDrawCountPositiveForAllConfiguredGames(): void
    {
        foreach (Config::games() as $game) {
            $this->assertGreaterThan(
                0,
                GameGenerateRegistry::drawCount($game['slug']),
                'Expected draw history for slug: ' . $game['slug']
            );
        }
    }

    public function testGenerateLottoWithoutDownload(): void
    {
        $result = GameGenerateRegistry::generate('lotto', false);

        $this->assertNotNull($result);
        $this->assertArrayHasKey('gameName', $result);
        $this->assertArrayHasKey('lines', $result);
        $this->assertSame('Lotto', $result['gameName']);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function unsupportedSlugProvider(): array
    {
        return [
            'unknown' => ['unknown-game'],
        ];
    }

    #[DataProvider('unsupportedSlugProvider')]
    public function testGenerateReturnsNullForUnsupportedSlug(string $slug): void
    {
        $this->assertNull(GameGenerateRegistry::generate($slug, false));
        $this->assertSame(0, GameGenerateRegistry::drawCount($slug));
        $this->assertSame('Unsupported game', GameGenerateRegistry::refreshDownload($slug));
    }
}
