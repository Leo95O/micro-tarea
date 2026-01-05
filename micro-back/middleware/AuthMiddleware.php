<?php

namespace Middleware;

use Slim\Slim;
use Firebase\JWT\JWT;
use Firebase\JWT\Key; // Necesario en versiones nuevas, pero en v5 usamos array o string directo
use Exception;

class AuthMiddleware
{
    /**
     * Método estático para verificar el Token manual o vía Middleware
     * @return object Datos del usuario (stdClass)
     */
    public static function verify()
    {
        $app = Slim::getInstance();
        $authHeader = $app->request->headers->get('Authorization');
        $secret = getenv('JWT_SECRET');

        // 1. Verificar si existe la cabecera
        if (!$authHeader) {
            $app->halt(401, json_encode([
                'status' => 'error',
                'message' => 'Token de acceso no proporcionado.'
            ]));
        }

        // 2. Limpiar el string "Bearer "
        // A veces llega como "Bearer eyJ..." y a veces solo el token
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $jwt = $matches[1];
        } else {
            $jwt = $authHeader;
        }

        try {
            // 3. Decodificar Token (Librería Firebase JWT v5)
            // En v5, el algoritmo se pasa en un array: ['HS256']
            $decoded = JWT::decode($jwt, $secret, ['HS256']);

            // 4. Kill-Switch (Opcional pero recomendado)
            // Verificar si el usuario sigue activo en BD
            // (Si lo despidieron hace 1 minuto, su token sigue válido, pero aquí lo frenamos)
            /* $db = $app->db;
            $stmt = $db->prepare("SELECT activo FROM usuarios WHERE id = ?");
            $stmt->execute([$decoded->sub]);
            $user = $stmt->fetch();
            
            if (!$user || $user['activo'] == 0) {
                 throw new Exception("Usuario inactivo o eliminado.");
            }
            */

            // 5. Inyectar datos del usuario en la App para usarlos en el Controlador
            $app->user_id = $decoded->sub;       // ID del usuario (ej: 5)
            $app->id_empresa = $decoded->empresa; // ID empresa (SaaS)
            
            return $decoded;

        } catch (Exception $e) {
            // Si el token expiró o es falso
            $app->halt(401, json_encode([
                'status' => 'error',
                'message' => 'Acceso denegado: ' . $e->getMessage()
            ]));
        }
    }
}