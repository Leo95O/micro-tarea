<?php

namespace App\Services;

use App\Repositories\UsuarioRepository;
use Firebase\JWT\JWT;
use Exception;

class AuthService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new UsuarioRepository();
    }

    public function login($email, $password)
    {
        $usuario = $this->repo->findByEmail($email);

        if (!$usuario) {
            throw new Exception("Credenciales incorrectas.");
        }

        // Verificar Password
        // NOTA: Asegúrate de usar password_hash() al crear usuarios para que esto funcione
        if (!password_verify($password, $usuario['password_hash'])) {
             throw new Exception("Credenciales incorrectas.");
        }

        // Generar Token JWT
        $issuedAt = time();
        $expirationTime = $issuedAt + (60 * 60 * 8); // 8 horas de validez
        $secret = getenv('JWT_SECRET'); // Leemos del .env
        
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'sub' => $usuario['id'],          // ID Usuario
            'empresa' => $usuario['id_empresa'], // ID Empresa (SaaS)
            'nivel' => $usuario['nivel_jerarquia'], // Nivel para validar permisos
            'nombre' => $usuario['nombre_completo'],
            'cargo' => $usuario['nombre_cargo']
        ];

        // Algoritmo HS256 compatible con Firebase JWT v5.5 (tu composer.json)
        $jwt = JWT::encode($payload, $secret, 'HS256'); 

        return [
            'token' => $jwt,
            'usuario' => [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre_completo'],
                'cargo' => $usuario['nombre_cargo'],
                'nivel' => $usuario['nivel_jerarquia']
            ]
        ];
    }
}