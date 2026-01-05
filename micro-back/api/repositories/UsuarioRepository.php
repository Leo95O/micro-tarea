<?php

namespace App\Repositories;

use Core\DB;
use PDO;

class UsuarioRepository
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    // Para el Login (Ya lo tenías)
    public function findByEmail($email)
    {
        $sql = "SELECT u.*, c.nivel_jerarquia, c.nombre as nombre_cargo 
                FROM usuarios u
                JOIN cargos c ON u.id_cargo = c.id
                WHERE u.email = :email AND u.activo = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    // [NUEVO] Verificar si email existe (para evitar duplicados)
    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    // [NUEVO] Crear Usuario
    public function create($data)
    {
        $sql = "INSERT INTO usuarios (
                    id_empresa, id_sucursal, id_cargo, 
                    nombre_completo, email, password_hash, 
                    activo
                ) VALUES (
                    :id_empresa, :id_sucursal, :id_cargo, 
                    :nombre, :email, :pass, 
                    1
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_empresa' => $data['id_empresa'],
            ':id_sucursal' => $data['id_sucursal'],
            ':id_cargo'   => $data['id_cargo'],
            ':nombre'     => $data['nombre_completo'],
            ':email'      => $data['email'],
            ':pass'       => $data['password_hash']
        ]);

        return $this->db->lastInsertId();
    }
}