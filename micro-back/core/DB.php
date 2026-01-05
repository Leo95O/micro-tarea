<?php

namespace Core;

use PDO;
use PDOException;

class DB {
    // Instancia estática para el Singleton
    private static $instance = null;
    private $pdo;

    // Constructor privado para evitar 'new DB()'
    private function __construct() {
        // Leemos las variables del .env usando getenv() o $_ENV
        $host = getenv('DB_HOST');
        $db   = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');
        $charset = getenv('DB_CHARSET');

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanzar errores como excepciones
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devolver arrays asociativos
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Usar sentencias preparadas reales (Seguridad)
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Si falla, matamos el proceso con un mensaje JSON (API Friendly)
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Error Crítico de Base de Datos',
                'detail' => $e->getMessage() // Ocultar en producción
            ]);
            exit;
        }
    }

    // Método mágico para obtener la instancia única
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
    
    // Evitar clonación
    private function __clone() {}
}