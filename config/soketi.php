<?php

return [
    'default_app' => [
        'id' => env('SOKETI_DEFAULT_APP_ID', 'app-id'),
        'key' => env('SOKETI_DEFAULT_APP_KEY', 'app-key'),
        'secret' => env('SOKETI_DEFAULT_APP_SECRET', 'app-secret'),
        'enable_client_messages' => false,
        'enable_statistics' => true,
    ],
]; 