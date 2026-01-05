<?php
/** @var \Slim\Slim $app */
$app = require_once __DIR__ . '/../../config/bootstrap.php';

use App\Controllers\SubtareaController;
use Middleware\AuthMiddleware;

$controller = new SubtareaController();

$app->hook('slim.before.dispatch', function() use ($app) {
    AuthMiddleware::verify();
});

$app->post('/', function() use ($controller) {
    $controller->create();
});

$app->get('/tarea/:idTarea', function($idTarea) use ($controller) {
    $controller->getAll($idTarea);
});

$app->put('/:id/toggle', function($id) use ($controller) {
    $controller->toggle($id);
});

$app->delete('/:id', function($id) use ($controller) {
    $controller->delete($id);
});

$app->run();