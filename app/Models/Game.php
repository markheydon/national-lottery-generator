<?php

namespace App\Models;

/**
 * Class Game
 *
 * Represents a lottery game configuration.
 * This is no longer a database model - games are now defined in config/games.php.
 *
 * @package App\Models
 */
class Game
{
    /**
     * @var string The game slug (URL identifier)
     */
    private string $slug;

    /**
     * @var string The game name
     */
    private string $name;

    /**
     * @var string The game logo filename
     */
    private string $logo;

    /**
     * Game constructor.
     *
     * @param string $slug Game slug
     * @param string $name Game name
     * @param string $logo Game logo filename
     */
    public function __construct(string $slug, string $name, string $logo)
    {
        $this->slug = $slug;
        $this->name = $name;
        $this->logo = $logo;
    }

    /**
     * Get all games from configuration.
     *
     * @return array<Game>
     */
    public static function all(): array
    {
        $gamesConfig = config('games.games', []);
        $games = [];
        
        foreach ($gamesConfig as $gameConfig) {
            $games[] = new self(
                $gameConfig['slug'],
                $gameConfig['name'],
                $gameConfig['logo']
            );
        }
        
        return $games;
    }

    /**
     * Find a game by slug.
     *
     * @param string $slug Game slug
     * @return Game|null
     */
    public static function findBySlug(string $slug): ?Game
    {
        $games = self::all();
        
        foreach ($games as $game) {
            if ($game->slug === $slug) {
                return $game;
            }
        }
        
        return null;
    }

    /**
     * Get the game slug.
     *
     * @return string The game slug.
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * The game name.
     *
     * @return string The game name.
     */
    public function getGameName(): string
    {
        return $this->name;
    }

    /**
     * The game logo filename.
     *
     * Contains the name only, no reference to possible location for reading.
     *
     * @return string The game logo filename.
     */
    public function getGameLogo(): string
    {
        return $this->logo;
    }

    /**
     * Get the Downloader instance for this game's CSV data.
     *
     * @return \App\Services\Lottery\Downloader|null Downloader instance or null if game doesn't have one
     */
    public function getDownloader(): ?\App\Services\Lottery\Downloader
    {
        return match (strtolower($this->name)) {
            'lotto', 'lotto hotpicks' => new \App\Services\Lottery\Downloader(
                \App\Services\Lottery\LottoDownload::HISTORY_DOWNLOAD_URL,
                \App\Services\Lottery\LottoDownload::FILENAME
            ),
            'euromillions', 'euromillions hotpicks' => new \App\Services\Lottery\Downloader(
                \App\Services\Lottery\EuromillionsDownload::HISTORY_DOWNLOAD_URL,
                \App\Services\Lottery\EuromillionsDownload::FILENAME
            ),
            'thunderball' => new \App\Services\Lottery\Downloader(
                \App\Services\Lottery\ThunderballDownload::HISTORY_DOWNLOAD_URL,
                \App\Services\Lottery\ThunderballDownload::FILENAME
            ),
            default => null,
        };
    }
}
