<?php

declare(strict_types=1);

namespace App\Services\Lottery;

/**
 * Maps game slugs to download, read, and generate service callables.
 */
final class GameGenerateRegistry
{
    public static function supports(string $slug): bool
    {
        return isset(self::entries()[$slug]);
    }

    public static function refreshDownload(string $slug): string
    {
        $entry = self::entries()[$slug] ?? null;

        if ($entry === null) {
            return 'Unsupported game';
        }

        return ($entry['download'])();
    }

    public static function drawCount(string $slug): int
    {
        $entry = self::entries()[$slug] ?? null;

        if ($entry === null) {
            return 0;
        }

        return count(($entry['readDraws'])());
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function generate(string $slug, bool $downloadNeeded): ?array
    {
        $entry = self::entries()[$slug] ?? null;

        if ($entry === null) {
            return null;
        }

        if ($downloadNeeded) {
            ($entry['download'])();
        }

        return ($entry['generate'])();
    }

    /**
     * @return array<string, array{download: callable(): string, readDraws: callable(): array, generate: callable(): array}>
     */
    private static function entries(): array
    {
        static $entries = null;

        if ($entries !== null) {
            return $entries;
        }

        $lottoDownloadRead = [
            'download' => [LottoDownload::class, 'download'],
            'readDraws' => [LottoDownload::class, 'readLottoDrawHistory'],
        ];

        $euromillionsDownloadRead = [
            'download' => [EuromillionsDownload::class, 'download'],
            'readDraws' => [EuromillionsDownload::class, 'readEuromillionsDrawHistory'],
        ];

        $entries = [
            'lotto' => [
                ...$lottoDownloadRead,
                'generate' => [LottoGenerate::class, 'generate'],
            ],
            'lotto-hotpicks' => [
                ...$lottoDownloadRead,
                'generate' => [LottoHotpicksGenerate::class, 'generate'],
            ],
            'euromillions' => [
                ...$euromillionsDownloadRead,
                'generate' => [EuromillionsGenerate::class, 'generate'],
            ],
            'euromillions-hotpicks' => [
                ...$euromillionsDownloadRead,
                'generate' => [EuromillionsHotpicksGenerate::class, 'generate'],
            ],
            'thunderball' => [
                'download' => [ThunderballDownload::class, 'download'],
                'readDraws' => [ThunderballDownload::class, 'readThunderballDrawHistory'],
                'generate' => [ThunderballGenerate::class, 'generate'],
            ],
            'set-for-life' => [
                'download' => [SetForLifeDownload::class, 'download'],
                'readDraws' => [SetForLifeDownload::class, 'readSetForLifeDrawHistory'],
                'generate' => [SetForLifeGenerate::class, 'generate'],
            ],
        ];

        return $entries;
    }
}
