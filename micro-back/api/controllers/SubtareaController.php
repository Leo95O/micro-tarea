<?php

namespace App\Controllers;

use Slim\Slim;
use App\Services\SubtareaService;
use Exception;

class SubtareaController
{
    private $service;

    public function __construct()
    {
        $this->service = new SubtareaService();
    }

    public function create()
    {
        $app = Slim::getInstance();
        $body = json_decode($app->request->getBody(), true);

        try {
            $id = $this->service->agregar($body);
            echo json_encode(['status' => 'success', 'id' => $id]);
        } catch (Exception $e) {
            $app->halt(400, json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }

    public function getAll($idTarea)
    {
        echo json_encode(['status' => 'success', 'data' => $this->service->listar($idTarea)]);
    }

    public function toggle($id)
    {
        $app = Slim::getInstance();
        $body = json_decode($app->request->getBody(), true);
        
        try {
            $nuevoEstado = $this->service->cambiarEstado($id, $body['completado']);
            echo json_encode(['status' => 'success', 'completado' => $nuevoEstado]);
        } catch (Exception $e) {
            $app->halt(400, json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }
    
    public function delete($id) {
        $app = Slim::getInstance();
        try {
            $this->service->eliminar($id);
            echo json_encode(['status' => 'success', 'message' => 'Eliminado']);
        } catch (Exception $e) {
            $app->halt(400, json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }
}