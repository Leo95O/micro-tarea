<?php

namespace App\Services;

use App\Repositories\SubtareaRepository;
use Exception;

class SubtareaService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new SubtareaRepository();
    }

    public function agregar($data)
    {
        if (empty($data['id_tarea']) || empty($data['titulo'])) {
            throw new Exception("Faltan datos obligatorios.");
        }
        return $this->repo->create($data);
    }

    public function listar($idTarea)
    {
        return $this->repo->getByTarea($idTarea);
    }

    public function cambiarEstado($id, $completado)
    {
        // Convertir true/false de JS a 1/0 de MySQL
        $estado = ($completado === true || $completado == 1 || $completado == 'true') ? 1 : 0;
        $this->repo->toggle($id, $estado);
        return $estado;
    }
    
    public function eliminar($id) {
        $item = $this->repo->findById($id);
        if ($item && $item['es_bloqueada'] == 1) {
            throw new Exception("Esta subtarea está bloqueada y no se puede eliminar.");
        }
        $this->repo->delete($id);
    }
}