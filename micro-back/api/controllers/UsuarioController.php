<?php

namespace App\Controllers;

use Slim\Slim;
use App\Services\UsuarioService;
use Exception;

class UsuarioController
{
    private $service;

    public function __construct()
    {
        $this->service = new UsuarioService();
    }

    public function create()
    {
        $app = Slim::getInstance();
        $body = $app->request->getBody();
        $data = json_decode($body, true);

        try {
            // Seguridad SaaS: Forzamos que el nuevo usuario pertenezca 
            // a la misma empresa que quien lo está creando.
            $data['id_empresa'] = $app->id_empresa; 
            
            // Opcional: Obtener nivel del creador para validar permisos
            $nivelCreador = $app->nivel ?? 0;

            $id = $this->service->crearUsuario($data, $nivelCreador);

            $app->response->setStatus(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Usuario creado correctamente',
                'id' => $id
            ]);

        } catch (Exception $e) {
            $app->halt(400, json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }
}