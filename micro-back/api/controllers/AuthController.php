<?php

namespace App\Controllers;

use Slim\Slim;
use App\Services\AuthService;
use Exception;

class AuthController
{
    private $service;

    public function __construct()
    {
        $this->service = new AuthService();
    }

    public function login()
    {
        $app = Slim::getInstance();
        $body = $app->request->getBody();
        $data = json_decode($body, true);

        try {
            if (empty($data['email']) || empty($data['password'])) {
                throw new Exception("Faltan datos.");
            }

            $resultado = $this->service->login($data['email'], $data['password']);

            echo json_encode(['status' => 'success', 'data' => $resultado]);

        } catch (Exception $e) {
            // Retardo anti-fuerza bruta
            sleep(1);
            $app->halt(401, json_encode(['status' => 'error', 'message' => $e->getMessage()]));
        }
    }
}