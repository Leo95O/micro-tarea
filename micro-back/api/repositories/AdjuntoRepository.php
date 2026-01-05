<?php

namespace App\Repositories;

use Core\DB;

class AdjuntoRepository
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    public function create($data)
    {
        $sql = "INSERT INTO adjuntos (id_tarea, id_uploader, tipo, ruta_archivo, nombre_original, peso_kb) 
                VALUES (:idt, :idu, :tipo, :ruta, :nom, :peso)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':idt' => $data['id_tarea'],
            ':idu' => $data['id_uploader'],
            ':tipo' => $data['tipo'],
            ':ruta' => $data['ruta_archivo'],
            ':nom' => $data['nombre_original'],
            ':peso' => $data['peso_kb']
        ]);
        return $this->db->lastInsertId();
    }

    public function getByTarea($idTarea)
    {
        $stmt = $this->db->prepare("SELECT * FROM adjuntos WHERE id_tarea = :id");
        $stmt->execute([':id' => $idTarea]);
        return $stmt->fetchAll();
    }
}