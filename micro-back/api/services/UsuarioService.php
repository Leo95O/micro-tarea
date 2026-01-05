<?php

namespace App\Services;

use App\Repositories\UsuarioRepository;
use Exception;

class UsuarioService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new UsuarioRepository();
    }

    public function crearUsuario($data, $creadorNivel = 0)
    {
        // 1. Validar duplicados
        if ($this->repo->emailExists($data['email'])) {
            throw new Exception("El email ya está registrado.");
        }

        // 2. Validar seguridad mínima de contraseña
        if (strlen($data['password']) < 6) {
            throw new Exception("La contraseña debe tener al menos 6 caracteres.");
        }

        // 3. Encriptar Contraseña (OBLIGATORIO)
        // Esto genera el hash tipo "$2y$10$..."
        $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);

        // 4. Regla de Negocio: Solo jefes pueden crear usuarios (Opcional)
        // if ($creadorNivel < 50) { throw new Exception("No tienes permisos para crear usuarios."); }

        return $this->repo->create($data);
    }
}