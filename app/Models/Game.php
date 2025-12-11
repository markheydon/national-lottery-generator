<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Game
 *
 * @package App\Models
 *
 * @property string $last_history_download
 * @property string $game_name
 * @property string $game_logo
 */
class Game extends Model
{
    /**
     * The game name.
     *
     * @return string The game name.
     */
    public function getGameName(): string
    {
        return $this->game_name;
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
        return $this->game_logo;
    }

    /**
     * The last history download datetime.
     *
     * Will return January 1st, 1970 as a default.
     *
     * @return \DateTime The last history download datetime.
     * @throws \Exception new \DateTime could fail in theory but shouldn't.
     */
    public function getLastHistoryDownload(): \DateTime
    {
        $lastDownloaded = $this->last_history_download;
        $result = (null === $lastDownloaded)
            ? (new \DateTime())->setTimestamp(0)
            : new \DateTime($lastDownloaded);
        return $result;
    }
}
