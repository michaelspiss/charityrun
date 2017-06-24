<?php

require __DIR__ . '/../vendor/autoload.php';

$container = new \Slim\Container([
    'database' => function() {
        return new PDO('sqlite:tests/storage/database/database.sqlite');
    }
]);

require __DIR__ . '/../app/helpers.php';