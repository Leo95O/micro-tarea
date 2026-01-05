<?php
/** @var \Slim\Slim $app */
$app = require_once __DIR__ . '/../../config/bootstrap.php';

use App\Controllers\AuthController;

$controller = new AuthController();

// Ruta pública (NO usamos AuthMiddleware aquí porque es para obtener el token)
$app->post('/login', function() use ($controller) {
    $controller->login();
});

$app->run();