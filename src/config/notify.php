<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for notification services like
    | Slack, Telegram, Discord, etc. used for deployment notifications.
    |
    */

    'default' => env('NOTIFY_DEFAULT', 'slack'),

    'channels' => [

        'slack' => [
            'enabled' => env('SLACK_NOTIFY_ENABLED', false),
            'webhook_url' => env('SLACK_WEBHOOK_URL'),
            'channel' => env('SLACK_CHANNEL', '#deployments'),
            'username' => env('SLACK_USERNAME', 'Laravel DevOps'),
            'icon_emoji' => env('SLACK_ICON_EMOJI', ':rocket:'),
        ],

        'telegram' => [
            'enabled' => env('TELEGRAM_NOTIFY_ENABLED', false),
            'bot_token' => env('TELEGRAM_BOT_TOKEN'),
            'chat_id' => env('TELEGRAM_CHAT_ID'),
        ],

        'discord' => [
            'enabled' => env('DISCORD_NOTIFY_ENABLED', false),
            'webhook_url' => env('DISCORD_WEBHOOK_URL'),
            'username' => env('DISCORD_USERNAME', 'Laravel DevOps'),
            'avatar_url' => env('DISCORD_AVATAR_URL'),
        ],

        'webhook' => [
            'enabled' => env('WEBHOOK_NOTIFY_ENABLED', false),
            'url' => env('WEBHOOK_URL'),
            'method' => env('WEBHOOK_METHOD', 'POST'),
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => env('WEBHOOK_AUTH_HEADER'),
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Templates
    |--------------------------------------------------------------------------
    |
    | Customize the notification messages for different deployment events.
    |
    */

    'templates' => [
        'success' => [
            'title' => '🚀 Deployment Successful',
            'message' => 'Deployment to {environment} completed successfully!',
            'color' => 'good',
        ],
        'failed' => [
            'title' => '❌ Deployment Failed',
            'message' => 'Deployment to {environment} failed!',
            'color' => 'danger',
        ],
        'started' => [
            'title' => '⏳ Deployment Started',
            'message' => 'Deployment to {environment} has started...',
            'color' => 'warning',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment Information
    |--------------------------------------------------------------------------
    |
    | Information about the current environment for notifications.
    |
    */

    'environment' => [
        'name' => env('APP_ENV', 'production'),
        'url' => env('APP_URL', 'http://localhost'),
        'version' => env('APP_VERSION', '1.0.0'),
    ],

];
