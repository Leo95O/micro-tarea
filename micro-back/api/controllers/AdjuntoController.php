<?php

namespace App\Controllers;

use Slim\Slim;
use App\Services\AdjuntoService;
use Exception;

class AdjuntoController
{
    private $service;

    public function __construct()
    {
        $this->service = new AdjuntoService();
    }

    public function upload()
    {
        $app = Slim::getInstance();
        // Nota: En Slim 2, $_FILES se accede nativamente o vía request
        // id_tarea viene como campo de texto en el form-data
        $idTarea = $app->request->post('id_tarea');
        
        if (empty($idTarea) || empty($_FILES['archivo'])) {
            $app->halt(400, json_encode(['status'=>'error', 'message'=>'Falta tarea o archivo']));
        }

        try {
            $id = $this->service->subirArchivo(
                $idTarea, 
                $app->user_id, 
                $app->id_empresa, 
                $_FILES['archivo']
            );
            echo json_encode(['status' => 'success', 'id' => $id]);
        } catch (Exception $e) {
            $app->halt(500, json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }
    
    public function getAll($idTarea) {
        echo json_encode(['status' => 'success', 'data' => $this->service->listar($idTarea)]);
    }
}