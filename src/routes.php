<?php

global $app;

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

/*
$app->get('/', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
*/


/*
$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
*/


$app->get('/home', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
    $response->getBody()->write("Hello, Word");
    $todo = new ToDo();
    // Render index view
   return $this->renderer->render($response, 'index.phtml', $args);
//    return $response;
});

// home
$app->get('/', \HomeController::class . ':home');

$app->get('/paieska/{fragment}', \HomeController::class . ':viewSearchHome');

$app->post('/search-pass-fragment', \HomeController::class . ':passSearchHome');


//$app->get('/login', \ToDoController::class . ':login');
//$app->get('/login', \HomeController::class . ':login');

//$app->post('/authenticateUser', \HomeController::class . ':authenticateUser');
//$app->post('/authenticateUser', \ToDoController::class . ':authenticateUser');

$app->get('/create', \ToDoController::class . ':create');

//$app->get('/view/{id}', \ToDoController::class . ':view');

$app->get('/edit/{id}', \ToDoController::class . ':edit');

$app->post('/update/{id}', \ToDoController::class . ':update');

$app->post('/addd', \ToDoController::class . ':addD');

$app->post('/quickAdd', \ToDoController::class . ':quickAdd');

//$app->post('/delete', \ToDoController::class . ':delete');

//gyvunai
$app->get('/gyvunai', \GyvunasController::class . ':viewAll');

$app->get('/gyvunas/{id}', \GyvunasController::class . ':viewOne');

$app->get('/sukurti-gyvuna', \GyvunasController::class . ':loadToFormForAdd');

$app->post('/prideti-gyvuna', \GyvunasController::class . ':addFromForm');

$app->post('/quickAddOne', \GyvunasController::class . ':quickAddOneFromForm');

$app->get('/redaguoti-gyvuna/{id}', \GyvunasController::class . ':loadToFormForEdit');

$app->post('/atnaujinti-gyvuna/{id}', \GyvunasController::class . ':updateFromForm');

$app->post('/istrinti', \GyvunasController::class . ':deleteFromForm');

//anketos
$app->get('/anketos/paieska/{fragment}', \AnketaController::class . ':viewSearchAnketa');

$app->post('/anketos/search-pass-fragment', \AnketaController::class . ':passSearchAnketa');

$app->get('/anketos', \AnketaController::class . ':viewAll');

$app->get('/anketa/{id}', \AnketaController::class . ':viewOne');

$app->get('/sukurti-anketa', \AnketaController::class . ':loadToFormForAdd');

$app->post('/prideti-anketa', \AnketaController::class . ':addFromForm');

$app->get('/redaguoti-anketa/{id}', \AnketaController::class . ':loadToFormForEdit');

$app->post('/atnaujinti-anketa/{id}', \AnketaController::class . ':updateFromForm');

$app->post('/ikelti-pagrind-nuotrauka/{id}', \AnketaController::class . ':uploadMainPhoto');

$app->post('/padaryti-pagrindine-nuotrauka/{id}', \AnketaController::class . ':makeMainPhoto');

$app->post('/istrinti-anketa', \AnketaController::class . ':deleteFromForm');

//skelbimai
$app->get('/skelbimo/paieska/{fragment}', \SkelbimasController::class . ':viewSearchSkelbima');

$app->post('/skelbimo/search-pass-fragment', \SkelbimasController::class . ':passSearchSkelbimas');

$app->get('/skelbimai', \SkelbimasController::class . ':viewAll');

$app->get('/skelbimas/{id}', \SkelbimasController::class . ':viewOne');

$app->get('/sukurti-skelbima', \SkelbimasController::class . ':loadToFormForAdd');

$app->post('/prideti-skelbima', \SkelbimasController::class . ':addNewFromForm');

$app->get('/redaguoti-skelbima/{id}', \SkelbimasController::class . ':loadToFormForEdit');

$app->post('/atnaujinti-skelbima/{id}', \SkelbimasController::class . ':updateFromForm');

$app->post('/istrinti-skelbima', \SkelbimasController::class . ':istrintiSkelbima');

$app->post('/rasyti-nauja-zinute/{id}', \SkelbimasController::class . ':rasytiNaujaZinute');

//vartotojai
$app->get('/login', \VartotojasController::class . ':login');

$app->post('/authenticateUser', \VartotojasController::class . ':authenticateUser');

$app->get('/iseiti', \VartotojasController::class . ':iseiti');

$app->get('/vartotojai', \VartotojasController::class . ':viewAll');

$app->get('/vartotojas/{id}', \VartotojasController::class . ':view');

$app->get('/sukurti-vartotoja', \VartotojasController::class . ':loadToFormForAdd');

$app->post('/registruoti', \VartotojasController::class . ':addFromForm');

$app->post('/quickAddOneVartot', \VartotojasController::class . ':quickAddOneFromForm');

$app->get('/redaguoti-vartotoja/{id}', \VartotojasController::class . ':edit');

$app->post('/atnaujinti-vartotoja/{id}', \VartotojasController::class . ':update');

$app->post('/istrinti-vartotoja', \VartotojasController::class . ':deleteFromForm');
//medziagos
$app->get('/materials', \MedziagaController::class . ':viewAll');

$app->get('/edit-material/{id}', \MedziagaController::class . ':edit');

$app->post('/update-material/{id}', \MedziagaController::class . ':update');

$app->get('/create-material', \MedziagaController::class . ':loadToFormForAdd');

$app->post('/submit', \MedziagaController::class . ':addFromForm');
//Uzsakymai
$app->post('/contracts', \UzsakymasController::class . 'viewAll');
