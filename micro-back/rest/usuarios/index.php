<?php
/** @var \Slim\Slim $app */
$app = require_once __DIR__ . '/../../config/bootstrap.php';

use App\Controllers\UsuarioController;
use Middleware\AuthMiddleware;

$controller = new UsuarioController();

// PROTECCIÓN: Solo usuarios logueados pueden crear otros usuarios
$app->hook('slim.before.dispatch', function() use ($app) {
    AuthMiddleware::verify();
});

// POST: Crear Usuario
$app->post('/', function() use ($controller) {
    $controller->create();
});

$app->run();