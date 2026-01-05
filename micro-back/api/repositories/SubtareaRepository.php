<?php

namespace App\Repositories;

use Core\DB;
use PDO;

class SubtareaRepository
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    public function create($data)
    {
        $sql = "INSERT INTO subtareas (id_tarea, titulo, descripcion, completado, es_bloqueada) 
                VALUES (:id_tarea, :titulo, :descripcion, 0, :bloq)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_tarea' => $data['id_tarea'],
            ':titulo' => $data['titulo'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':bloq' => $data['es_bloqueada'] ?? 0
        ]);
        return $this->db->lastInsertId();
    }

    public function getByTarea($idTarea)
    {
        $stmt = $this->db->prepare("SELECT * FROM subtareas WHERE id_tarea = :id");
        $stmt->execute([':id' => $idTarea]);
        return $stmt->fetchAll();
    }

    public function toggle($id, $estado)
    {
        $stmt = $this->db->prepare("UPDATE subtareas SET completado = :est WHERE id = :id");
        $stmt->execute([':est' => $estado, ':id' => $id]);
    }

    public function delete($id)
    {
        // Solo borramos si NO es bloqueada (validar en servicio)
        $stmt = $this->db->prepare("DELETE FROM subtareas WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM subtareas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}