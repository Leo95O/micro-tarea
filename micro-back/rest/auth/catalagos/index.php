<?php
/** @var \Slim\Slim $app */
$app = require_once __DIR__ . '/../../config/bootstrap.php';

use App\Controllers\CatalogoController;
use Middleware\AuthMiddleware;

$controller = new CatalogoController();

$app->hook('slim.before.dispatch', function() use ($app) {
    AuthMiddleware::verify();
});

$app->get('/sucursales', function() use ($controller) {
    $controller->getSucursales();
});

$app->get('/areas/:id', function($id) use ($controller) {
    $controller->getAreas($id);
});

$app->get('/cargos', function() use ($controller) {
    $controller->getCargos();
});

$app->run();