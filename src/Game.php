<?php

declare(strict_types=1);

namespace App;

use App\Services\Lottery\Downloader;
use App\Services\Lottery\EuromillionsDownload;
use App\Services\Lottery\LottoDownload;
use App\Services\Lottery\SetForLifeDownload;
use App\Services\Lottery\ThunderballDownload;

/**
 * Represents a lottery game configuration.
 */
class Game
{
    private string $slug;

    private string $name;

    private string $logo;

    private ?Downloader $downloader = null;

    /** @var array<string, Game> */
    private static array $instances = [];

    public function __construct(string $slug, string $name, string $logo)
    {
        $this->slug = $slug;
        $this->name = $name;
        $this->logo = $logo;
    }

    /**
     * @return array<Game>
     */
    public static function all(): array
    {
        $games = [];

        foreach (Config::games() as $gameConfig) {
            $slug = $gameConfig['slug'];

            if (! isset(self::$instances[$slug])) {
                self::$instances[$slug] = new self(
                    $slug,
                    $gameConfig['name'],
                    $gameConfig['logo']
                );
            }

            $games[] = self::$instances[$slug];
        }

        return $games;
    }

    public static function findBySlug(string $slug): ?self
    {
        if (isset(self::$instances[$slug])) {
            return self::$instances[$slug];
        }

        self::all();

        return self::$instances[$slug] ?? null;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getGameName(): string
    {
        return $this->name;
    }

    public function getGameLogo(): string
    {
        return $this->logo;
    }

    public function getDownloader(): ?Downloader
    {
        if ($this->downloader !== null) {
            return $this->downloader;
        }

        $this->downloader = match ($this->slug) {
            'lotto', 'lotto-hotpicks' => new Downloader(
                LottoDownload::HISTORY_DOWNLOAD_URL,
                LottoDownload::FILENAME
            ),
            'euromillions', 'euromillions-hotpicks' => new Downloader(
                EuromillionsDownload::HISTORY_DOWNLOAD_URL,
                EuromillionsDownload::FILENAME
            ),
            'thunderball' => new Downloader(
                ThunderballDownload::HISTORY_DOWNLOAD_URL,
                ThunderballDownload::FILENAME
            ),
            'set-for-life' => new Downloader(
                SetForLifeDownload::HISTORY_DOWNLOAD_URL,
                SetForLifeDownload::FILENAME
            ),
            default => null,
        };

        return $this->downloader;
    }
}
