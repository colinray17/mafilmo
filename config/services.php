<?php

return [

    'tmdb' => [
        'key'       => env('TMDB_API_KEY'),
        'base_url'  => env('TMDB_BASE_URL', 'https://api.themoviedb.org/3'),
        'image_url' => env('TMDB_IMAGE_URL', 'https://image.tmdb.org/t/p/w500'),
    ],

];
