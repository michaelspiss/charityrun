<?php
return [
    'settings' => [
        'displayErrorDetails' => false, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../resources/views/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../storage/logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Translator settings
        'translator' => [
            'path' => __DIR__ . '/../resources/lang',
            'default_locale' => 'en'
        ],

        'database' => [
            // sqlite
            'dsn' => 'sqlite:../storage/database/database.sqlite',
            'username' => null,
            'password' => null,
            // mysql
            // 'dsn' => 'mysql:host=127.0.0.1;dbname=database;',
            // 'username' => 'root',
            // 'password' => '123456'
        ],
    ],
];
