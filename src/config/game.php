<?php

return [

    'reset_cards_every_round' => env('GAME_RESET_CARDS_EVERY_ROUND', false),

    'cards_per_user' => env('GAME_CARDS_PER_USER', 11),

    'min_users_for_round' => env('GAME_MIN_USERS_FOR_ROUND', 3),

    'card_random' => [

        'usage_count_multiplier' => intval(env('GAME_CARDS_USAGE_COUNT_MULTIPLIER', 1)),

        'win_count_multiplier' => intval(env('GAME_CARDS_WIN_COUNT_MULTIPLIER', -1)),

        'random_multiplier' => intval(env('GAME_CARDS_RANDOM_MULTIPLIER', 20)),

        'days_multiplier' => intval(env('GAME_CARDS_DAYS_MULTIPLIER', 1))

    ]

];
