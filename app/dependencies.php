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
    if(!isset($_SESSION['lang'])) {
		chdir($settings['path']);
	    $available_languages = array_diff(glob('*', GLOB_ONLYDIR), ['.', '..']);
	    chdir(__DIR__);
		$_SESSION['lang'] = preferred_language(
			$settings['default_locale'],
			$available_languages,
			$_SERVER['HTTP_ACCEPT_LANGUAGE']);
    }
    return new \MichaelSpiss\Translation\Translator($_SESSION['lang'], $settings['path']);
};

$container['database'] = function ($c) {
    $settings = $c->get('settings')['database'];
    $pdo = new PDO($settings['dsn'], $settings['username'], $settings['password']);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['auth'] = function () {
    $storageDelegate = new \App\Auth\PDOStorage(app('database'));
    $sessionDelegate = new \Solution10\Auth\Driver\Session();

    return new Solution10\Auth\Auth('Default', $sessionDelegate, $storageDelegate);
};