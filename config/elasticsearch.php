<?php

return [
    'hosts' => [
        env('ELASTICSEARCH_HOST', 'https://localhost:9200'),
    ],
    'auth' => [
        'username' => env('ELASTICSEARCH_USER', 'elastic'),
        'password' => env('ELASTICSEARCH_PASS', ''),
    ],
    'ssl' => [
        'verify' => false
    ],
];
