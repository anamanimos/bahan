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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'erp' => [
        'uuid' => env('ERP_SSO_UUID'),
        'url' => env('ERP_SSO_URL'),
        'webhook_url' => env('ERP_WEBHOOK_URL'),
    ],

    'damaijaya' => [
        'url' => env('DAMAIJAYA_API_URL', 'https://app.damaijaya.my.id/api/v1'),
        'token' => env('DAMAIJAYA_API_TOKEN', 'a6067c77b6e8ae671f23f56fe29550a070079f10b2a1097381bf845d8f93c81e'),
    ],

];
