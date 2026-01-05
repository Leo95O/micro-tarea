<?php

namespace App\Repositories;

use Core\DB;
use PDO;

class CatalogoRepository
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    public function listarSucursales($idEmpresa)
    {
        $stmt = $this->db->prepare("SELECT id, nombre FROM sucursales WHERE id_empresa = :emp AND activo = 1");
        $stmt->execute([':emp' => $idEmpresa]);
        return $stmt->fetchAll();
    }

    public function listarAreas($idSucursal)
    {
        $stmt = $this->db->prepare("SELECT id, nombre FROM areas WHERE id_sucursal = :suc AND activo = 1");
        $stmt->execute([':suc' => $idSucursal]);
        return $stmt->fetchAll();
    }

    public function listarCargos($idEmpresa)
    {
        $stmt = $this->db->prepare("SELECT id, nombre, nivel_jerarquia FROM cargos WHERE id_empresa = :emp");
        $stmt->execute([':emp' => $idEmpresa]);
        return $stmt->fetchAll();
    }
}
