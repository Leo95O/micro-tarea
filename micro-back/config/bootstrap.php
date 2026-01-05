<?php
// =================================================================
// BOOTSTRAP: El Archivo Maestro de Carga (PHP 7.1 + Slim 2.6)
// =================================================================

// 1. Cargar el Autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Slim\Slim;
use Core\DB;

// 2. Cargar Variables de Entorno (.env)
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::create(__DIR__ . '/../');
    $dotenv->load();
}

// 3. Configurar Slim Framework 2.6
$app = new Slim([
    'debug' => getenv('APP_DEBUG') === 'true',
    'mode'  => getenv('APP_ENV') ?: 'development'
]);

// 4. Configurar Cabeceras JSON por defecto
$app->response->headers->set('Content-Type', 'application/json');

// 5. Inyección de Dependencias (Base de Datos)
$app->container->singleton('db', function () {
    return DB::getInstance();
});

// 6. Registrar Middleware Globales (CORS)
// ¡Esto es vital para que Angular pueda conectarse!
$app->add(new \Middleware\CorsMiddleware());

// 7. Retornar la instancia de $app
return $app;