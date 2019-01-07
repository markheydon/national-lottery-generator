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
    }
}
