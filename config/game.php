<?php

return [

    'trigger_warning' => [

        'reset_cards_every_round' => env('GAME_RESET_CARDS_EVERY_ROUND', false),

        'cards_per_user' => env('GAME_CARDS_PER_USER', 11),

        'min_users_for_round' => env('GAME_MIN_USERS_FOR_ROUND', 3),

        'card_random' => [

            'usage_count_multiplier' => env('GAME_CARDS_USAGE_COUNT_MULTIPLIER'),

            'usage_count_limit' => env('GAME_CARDS_USAGE_COUNT_LIMIT'),

            'win_count_multiplier' => env('GAME_CARDS_WIN_COUNT_MULTIPLIER'),

            'random_multiplier' => env('GAME_CARDS_RANDOM_MULTIPLIER'),

            'days_multiplier' => env('GAME_CARDS_DAYS_MULTIPLIER'),

            'wait_hours' => intval(env('GAME_CARDS_WAIT_HOURS', 1))

        ]

    ],

    'one_word_each' => [

        'seconds' => intval(env('GAME_OWE_SECONDS', 30))

    ]


];
