<?php
// DIC configuration

$container = $app->getContainer();

// Alternative route strategy
$container['foundHandler'] = function () {
    return new \Slim\Handlers\Strategies\RequestResponseArgs();
};

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// translator
$container['translator'] = function ($c) {
    $settings = $c->get('settings')['translator'];
    $loader = new \Illuminate\Translation\FileLoader(new \Illuminate\Filesystem\Filesystem, $settings['path']);
    return new \Illuminate\Translation\Translator($loader, $settings['default_locale']);
};

$container['database'] = function ($c) {
    $settings = $c->get('settings')['database'];
    return new PDO($settings['dsn'], $settings['username'], $settings['password']);
};