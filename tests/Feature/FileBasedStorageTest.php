<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Game;
use Illuminate\Support\Facades\Storage;

class FileBasedStorageTest extends TestCase
{
    /**
     * Test that games can be loaded from configuration.
     */
    public function test_games_load_from_config(): void
    {
        $games = Game::all();

        $this->assertIsArray($games);
        $this->assertGreaterThan(0, count($games));

        // Check that Lotto game exists
        $lottoGame = Game::findBySlug('lotto');
        $this->assertNotNull($lottoGame);
        $this->assertEquals('Lotto', $lottoGame->getGameName());
    }

    /**
     * Test that Game model can create a downloader.
     */
    public function test_game_can_create_downloader(): void
    {
        $game = Game::findBySlug('lotto');
        $this->assertNotNull($game);

        $downloader = $game->getDownloader();
        $this->assertNotNull($downloader);
        $this->assertInstanceOf(\App\Services\Lottery\Downloader::class, $downloader);
    }

    /**
     * Test that storage directory is accessible.
     */
    public function test_storage_directory_accessible(): void
    {
        // Ensure the lottery storage directory exists
        Storage::disk('local')->makeDirectory('lottery');

        $this->assertTrue(Storage::disk('local')->exists('lottery'));
    }

    /**
     * Test that homepage loads without database.
     */
    public function test_homepage_loads(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Lottery Generator');
        $response->assertSee('Lotto');
    }
}
