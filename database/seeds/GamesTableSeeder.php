<?php

use Illuminate\Database\Seeder;

class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('games')->insert([
            'game_name' => 'Lotto',
            'game_logo' => 'lotto_retina_text_below.png',
        ]);
        DB::table('games')->insert([
            'game_name' => 'EuroMillions',
            'game_logo' => 'euromillions_retina_text_below.png',
        ]);
        DB::table('games')->insert([
            'game_name' => 'Thunderball',
            'game_logo' => 'thunderball_retina_text_below.png',
        ]);
        DB::table('games')->insert([
            'game_name' => 'Lotto Hotpicks',
            'game_logo' => 'lotto-hotpicks_retina_text_below.png',
        ]);
        DB::table('games')->insert([
            'game_name' => 'EuroMillions Hotpicks',
            'game_logo' => 'euromillions-hotpicks-retina_text_below.png',
        ]);
    }
}
