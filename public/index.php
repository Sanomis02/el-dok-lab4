<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Access Control
require __DIR__ . '/../src/Acl.php';

// Register Classes
require __DIR__ . '/../src/classes/ToDo.php';

// Register Controllers
require __DIR__.'/../src/controllers/ToDoController.php';

// Register Gyvunas
require __DIR__ . '/../src/controllers/GyvunasController.php';

// Register Home
require __DIR__.'/../src/controllers/HomeController.php';

// Register Vartotojai
require __DIR__.'/../src/controllers/VartotojasController.php';

// Register Anketos
require __DIR__ . '/../src/controllers/AnketaController.php';

// Register Skelbimai
require __DIR__ . '/../src/controllers/SkelbimasController.php';

// Run app
$app->run();
