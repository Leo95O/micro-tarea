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
// Verificamos si existe para no romper en producción si usas variables de sistema
if (file_exists(__DIR__ . '/../.env')) {
    // Sintaxis compatible con vlucas/phpdotenv v3.x
    $dotenv = Dotenv::create(__DIR__ . '/../');
    $dotenv->load();
}

// 3. Configurar Slim Framework 2.6
// Instanciamos la App base aquí para que todos los endpoints la hereden
$app = new Slim([
    'debug' => getenv('APP_DEBUG') === 'true',
    'mode'  => getenv('APP_ENV') ?: 'development'
]);

// 4. Configurar Cabeceras JSON por defecto
// Esto evita tener que poner header('Content-Type: application/json') en cada controlador
$app->response->headers->set('Content-Type', 'application/json');

// 5. Inyección de Dependencias (El Contenedor de Slim 2)
// Guardamos la conexión DB en el contenedor para usarla globalmente como $app->db
$app->container->singleton('db', function () {
    return DB::getInstance();
});

// 6. Retornar la instancia de $app
// Esto es vital: los index.php de /rest harán: $app = require 'bootstrap.php';
return $app;