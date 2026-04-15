<?php

return [

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'simrs' => [
        'base_url'   => env('SIMRS_BASE_URL'),
        'token'      => env('SIMRS_TOKEN'),
        'timeout'    => (int) env('SIMRS_TIMEOUT', 30),
        'verify_ssl' => filter_var(env('SIMRS_VERIFY_SSL', true), FILTER_VALIDATE_BOOL),
    ],

];