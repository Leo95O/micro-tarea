<?php
/** @var \Slim\Slim $app */
$app = require_once __DIR__ . '/../../config/bootstrap.php';

use App\Controllers\AdjuntoController;
use Middleware\AuthMiddleware;

$controller = new AdjuntoController();

$app->hook('slim.before.dispatch', function() use ($app) {
    AuthMiddleware::verify();
});

// POST form-data: key "archivo", key "id_tarea"
$app->post('/', function() use ($controller) {
    $controller->upload();
});

$app->get('/tarea/:idTarea', function($idTarea) use ($controller) {
    $controller->getAll($idTarea);
});

$app->run();