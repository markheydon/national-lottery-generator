<?php

namespace App\Http\Controllers;

use App\Game;
use Illuminate\Http\Request;
use MarkHeydon\LotteryGenerator\EuromillionsGenerate;
use MarkHeydon\LotteryGenerator\LottoGenerate;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $games = Game::all();
        return view(
            'games.index',
            [
                'title' => 'Games',
                'games' => $games,
            ]
        );
    }

    /**
     * Displays generated number for the specified Game.
     *
     * @param Game $game Game to generate numbers for.
     *
     * @return \Illuminate\Http\Response
     */
    public function generate(Game $game)
    {
        switch (strtolower($game->game_name)) {
            case 'lotto':
                $generate = LottoGenerate::generate();
                break;
            case 'euromillions':
                $generate = EuromillionsGenerate::generate();
                break;
        }

        return view(
            'games.generate',
            [
                'title' => $game->game_name,
                'game' => $game,
                'generate' => $generate,
            ]
        );
    }
}
