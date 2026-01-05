<?php

namespace App\Controllers;

use Slim\Slim;
use App\Services\TareaService;
use Exception;

class TareaController
{
    private $service;

    public function __construct()
    {
        $this->service = new TareaService();
    }

    // POST: Crear Tarea
    public function create()
    {
        $app = Slim::getInstance();
        $body = $app->request->getBody();
        $data = json_decode($body, true);

        try {
            // Datos inyectados por el AuthMiddleware
            $usuarioId = $app->user_id;
            $empresaId = $app->id_empresa;

            $id = $this->service->crearTarea($data, $usuarioId, $empresaId);

            $app->response->setStatus(201);
            echo json_encode([
                'status' => 'success', 
                'message' => 'Tarea creada', 
                'id' => $id
            ]);
        } catch (Exception $e) {
            $app->halt(400, json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }

    // POST: /{id}/tomar (Pool)
    public function take($id)
    {
        $app = Slim::getInstance();
        try {
            $usuarioId = $app->user_id;
            $empresaId = $app->id_empresa;
            // Opcional: Obtener nivel real del usuario desde DB si no está en token
            $nivelUsuario = 10; // Placeholder, debería venir del Token o DB

            $this->service->tomarTareaDelPool($id, $usuarioId, $empresaId, $nivelUsuario);

            echo json_encode(['status' => 'success', 'message' => 'Tarea asignada correctamente.']);
        } catch (Exception $e) {
            $app->halt(409, json_encode(['status' => 'error', 'message' => $e->getMessage()])); // 409 Conflict
        }
    }

    // POST: /{id}/evaluar (Jefe valida)
    public function evaluate($id)
    {
        $app = Slim::getInstance();
        $body = $app->request->getBody();
        $data = json_decode($body, true); // Espera: { "accion": "CORREGIR", "nueva_fecha_fin": "..." }

        try {
            $empresaId = $app->id_empresa;
            $accion = $data['accion'] ?? null;

            $msg = $this->service->evaluarTarea($id, $accion, $data, $empresaId);

            echo json_encode(['status' => 'success', 'message' => $msg]);
        } catch (Exception $e) {
            $app->halt(400, json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }
}