<?php

namespace Middleware;

use Slim\Middleware;

class CorsMiddleware extends Middleware
{
    public function call()
    {
        // 1. Obtener la aplicación Slim y la respuesta
        $app = $this->app;
        $response = $app->response();

        // 2. Definir cabeceras permitidas
        // En producción, cambia '*' por 'http://localhost:4200' para más seguridad
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        // 3. Manejar la petición "Preflight" (OPTIONS)
        // Los navegadores mandan una petición OPTIONS antes de la real para ver si tienen permiso.
        if ($app->request()->isOptions()) {
            $response->setStatus(200);
            return; // Terminamos aquí, no procesamos nada más para OPTIONS
        }

        // 4. Si no es OPTIONS, dejar pasar la petición al siguiente nivel
        $this->next->call();
    }
}