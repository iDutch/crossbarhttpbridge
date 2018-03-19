<?php

return [
    'guzzle' => [
        'base_uri' => env('CROSSBAR_HTTP_BRIDGE_URI', null),
        'verify' => env('CROSSBAR_HTTP_BRIDGE_VERIFY_SSL', true),
    ],
    'options' => [
        'publish_path' => env('CROSSBAR_HTTP_BRIDGE_PUBLISH_PATH', null),
        'call_path' => env('CROSSBAR_HTTP_BRIDGE_CALL_PATH', null),
        'publisher_key' => env('CROSSBAR_HTTP_BRIDGE_PUBLISHER_KEY', null),
        'publisher_secret' => env('CROSSBAR_HTTP_BRIDGE_PUBLISHER_SECRET', null),
        'caller_key' => env('CROSSBAR_HTTP_BRIDGE_CALLER_KEY', null),
        'caller_secret' => env('CROSSBAR_HTTP_BRIDGE_CALLER_SECRET', null),
    ]
];
