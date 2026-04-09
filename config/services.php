<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'live_rates' => [
        'url' => env(
            'LIVE_RATES_URL',
            'https://bcast.suvidhigold.in:7768/VOTSBroadcastStreaming/Services/xml/GetLiveRateByTemplateID/suvidhi'
        ),
        'timeout_seconds' => env('LIVE_RATES_TIMEOUT_SECONDS', 15),
        'connect_timeout_seconds' => env('LIVE_RATES_CONNECT_TIMEOUT_SECONDS', 5),
        'cache_ttl_seconds' => env('LIVE_RATES_CACHE_TTL_SECONDS', 5),
        'rate_limit_per_minute' => env('LIVE_RATES_RATE_LIMIT_PER_MINUTE', 60),
    ],

];
