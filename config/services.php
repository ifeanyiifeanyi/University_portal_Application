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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
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

    'remita' => [
        'base_url' => env('REMITA_BASE_URL', 'https://demo.remita.net/remita/exapp/api/v1/send/api/'),
        'merchant_id' => env('REMITA_MERCHANT_ID', '2547916'),
        'api_key' => env('REMITA_API_KEY', '1946'),
        'service_type_id' => env('REMITA_SERVICE_TYPE_ID', '4430731'),
        'public_key' => env('REMITA_PUBLIC_KEY', 'QzAwMDAyNzEyNTl8MTEwNjE4NjF8OWZjOWYwNmMyZDk3MDRhYWM3YThiOThlNTNjZTE3ZjYxOTY5NDdmZWE1YzU3NDc0ZjE2ZDZjNTg1YWYxNWY3NWM4ZjMzNzZhNjNhZWZlOWQwNmJhNTFkMjIxYTRiMjYzZDkzNGQ3NTUxNDIxYWNlOGY4ZWEyODY3ZjlhNGUwYTY='),
        'secret_key' => env('REMITA_SECRET_KEY', '23093b2bda801eece94fc6e8363c05fad90a4ba3e12045005141b0bab41704b3a148904529239afd1c9a3880d51b4018d13fd626b2cef77a6f858fe854834e54'),
        'mode' => env('REMITA_MODE', 'test'), // 'test' or 'live'
    ],
    'paystack' => [
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
        'timeout' => 30, // Optional timeout configuration
    ],

];
