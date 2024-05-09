<?php
// DIC configuration

global $app;
$container = $app->getContainer();
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

// twig

$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);
    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());
    return $view;
};

// db

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
        $db['user'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};


$container['flash'] = function ($c) {
    return new \Slim\Flash\Messages();
};

$container['upload_directory'] = function ($c) {
    return __DIR__ . '/../public/assets/img/gyvuneliai';
};


// TodoController

$container['ToDoController'] = function($c) {
    $view = $c->get("view"); // retrieve the 'view' from the container
    $db = $c->get('db');
    $flash= $c->get("flash");
    return new ToDoController($view, $db, $flash);
};
// GyvunasController

$container['GyvunasController'] = function($c) {
    $view = $c->get("view"); // retrieve the 'view' from the container
    $db = $c->get('db');
    $flash= $c->get("flash");
    return new GyvunasController($view, $db, $flash);
};

// HomeController

$container['HomeController'] = function($c) {
    $view = $c->get("view"); // retrieve the 'view' from the container
    $db = $c->get('db');
    $flash= $c->get("flash");
    return new HomeController($view, $db, $flash);
};
// VartotojasController

$container['VartotojasController'] = function($c) {
    $view = $c->get("view"); // retrieve the 'view' from the container
    $db = $c->get('db');
    $flash= $c->get("flash");
    return new VartotojasController($view, $db, $flash);
};

$container['AnketaController'] = function($c) {
    $view = $c->get("view"); // retrieve the 'view' from the container
    $db = $c->get('db');
    $flash= $c->get("flash");
    $upload_directory = $c->get("upload_directory");
    return new AnketaController($view, $db, $flash, $upload_directory);
};

$container['SkelbimasController'] = function($c) {
    $view = $c->get("view"); // retrieve the 'view' from the container
    $db = $c->get('db');
    $flash= $c->get("flash");
    return new SkelbimasController($view, $db, $flash);
};

