<?php
/** @var \Slim\Slim $app */
$app = require_once __DIR__ . '/../../config/bootstrap.php';

use App\Controllers\TareaController;
use Middleware\AuthMiddleware;

// Instanciar controlador
$controller = new TareaController();

// Grupo de rutas protegidas
// Nota: Aquí deberíamos añadir ->add(new AuthMiddleware()) si Slim 2 lo soportara directo en grupos
// pero en Slim 2.6 a veces es mejor llamar AuthMiddleware::verify() dentro de la ruta o hook.
// Para este ejemplo, validamos al inicio:

$app->hook('slim.before.dispatch', function() use ($app) {
    // Si la ruta no es pública, validamos
    AuthMiddleware::verify(); 
});

// RUTAS
$app->post('/', function() use ($controller) { 
    $controller->create(); 
});

$app->post('/:id/tomar', function($id) use ($controller) { 
    $controller->take($id); 
});

$app->post('/:id/evaluar', function($id) use ($controller) { 
    $controller->evaluate($id); 
});

$app->run();