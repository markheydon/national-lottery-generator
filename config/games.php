<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Available Lottery Games
    |--------------------------------------------------------------------------
    |
    | This configuration defines all available lottery games in the application.
    | Each game has a slug (used in URLs), name, and logo filename.
    |
    */

    'games' => [
        [
            'slug' => 'lotto',
            'name' => 'Lotto',
            'logo' => 'lotto_retina_text_below.png',
        ],
        [
            'slug' => 'euromillions',
            'name' => 'EuroMillions',
            'logo' => 'euromillions_retina_text_below.png',
        ],
        [
            'slug' => 'thunderball',
            'name' => 'Thunderball',
            'logo' => 'thunderball_retina_text_below.png',
        ],
        [
            'slug' => 'lotto-hotpicks',
            'name' => 'Lotto Hotpicks',
            'logo' => 'lotto-hotpicks_retina_text_below.png',
        ],
        [
            'slug' => 'euromillions-hotpicks',
            'name' => 'EuroMillions Hotpicks',
            'logo' => 'euromillions-hotpicks-retina_text_below.png',
        ],
    ],
];
