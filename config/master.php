<?php

/**
 * Main Master Configuration.
 *
 * @version 1.0.0
 *
 * @license MIT
 */
return [
    'app' => [
        'profile' => [
            'name' => 'Condominios - Vzla.',
            'short_name' => 'Cond. Vzla.',
            'description' => 'Condominium Management System',
            'keywords' => 'Cond Vzla, Condominios, Venezuela',
            'author' => '@xanders80', // Your name or company
            'version' => '1.0.0', // major.minor.patch
            'laravel' => '12.0', // Laravel version
        ],
        'root' => [
            'backend' => 'App/Http/Controllers/Backend', // path to backend controller
            'frontend' => 'App/Http/Controllers/Frontend', // path to frontend controller
            'model' => 'App/Models', // path to model
            'view' => 'views/backend', // path to backend view
        ],
        'url' => [
            'backend' => 'admin', // url for backend
            'frontend' => 'web', // url for frontend
        ],
        'view' => [
            'backend' => 'backend', // path to backend view
            'frontend' => 'frontend', // path to frontend view
        ],
        'web' => [
            'template' => 'admins', // template for frontend view (default: xanders80)
            'backend' => 'backend', // path to backend
            'icon' => '',
            'logo_light' => '/images/apple-touch-icon.png',
            'logo_dark' => '/images/logo-main-master.png',
            'favicon' => '/images/favicon.ico',
            'background' => '/images/auth-bg/bg-1.jpg',
            'header_animation' => 'on', // turn on/off header animation
        ],
        'level' => [
            'read',
            'create',
            'update',
            'delete', // level of access for user role and permission module
        ],
    ],
    'content' => [
        'announcement' => [
            'status' => [
                'very_important' => 'Muy Importante',
                'important' => 'Importante',
                'normal' => 'Normal',
            ],
            'color' => [
                'very_important' => 'danger',
                'important' => 'warning',
                'normal' => 'info',
            ],
        ],
    ],
];
