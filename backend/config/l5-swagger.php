<?php

declare(strict_types=1);

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'HyggeStack API Documentation',
            ],
            'routes' => [
                'api' => 'api/documentation',
            ],
            'paths' => [
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', false),
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'format_to_use' => env('L5_SWAGGER_FORMAT', 'json'),
                'annotations' => [
                    base_path('app'),
                ],
            ],
        ],
    ],
    'defaults' => [
        'routes' => [
            'docs' => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],
            'group_by' => 'tags',
            'sort_by' => 'paths',
            'hide_from_docs' => [
                'sanctum',
            ],
        ],
        'paths' => [
            'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', false),
            'docs_json' => 'api-docs.json',
            'docs_yaml' => 'api-docs.yaml',
            'format_to_use' => env('L5_SWAGGER_FORMAT', 'json'),
            'annotations' => [
                base_path('app'),
            ],
        ],
        'swagger' => [
            'swagger' => '2.0',
            'info' => [
                'title' => 'HyggeStack API',
                'description' => 'A cozy blogging platform API',
                'version' => '1.0.0',
                'contact' => [
                    'email' => 'hello@hyggestack.local',
                ],
            ],
            'host' => env('APP_URL', 'http://localhost:8080'),
            'basePath' => '/api/v1',
            'schemes' => [
                'http',
                'https',
            ],
            'consumes' => [
                'application/json',
            ],
            'produces' => [
                'application/json',
            ],
        ],
    ],
];
