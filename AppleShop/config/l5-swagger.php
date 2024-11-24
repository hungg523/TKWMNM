<?php

return [
    'default' => 'swagger',
    'documentations' => [
        'swagger' => [
            'api' => [
                'title' => 'Laravel Swagger API',
            ],
            'routes' => [
                'api' => 'api/documentation',
            ],
            'paths' => [
                'annotations' => base_path('app'),
            ],
        ],
    ],
    'paths' => [
        'docs' => storage_path('api-docs'),
        'views' => resource_path('views/vendor/l5-swagger'),
    ],
];

