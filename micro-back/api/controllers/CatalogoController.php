<?php

namespace App\Controllers;

use Slim\Slim;
use App\Repositories\CatalogoRepository;

class CatalogoController
{
    private $repo;

    public function __construct()
    {
        $this->repo = new CatalogoRepository();
    }

    public function getSucursales()
    {
        $app = Slim::getInstance();
        $empresaId = $app->id_empresa;
        echo json_encode(['status' => 'success', 'data' => $this->repo->listarSucursales($empresaId)]);
    }

    public function getAreas($idSucursal)
    {
        echo json_encode(['status' => 'success', 'data' => $this->repo->listarAreas($idSucursal)]);
    }

    public function getCargos()
    {
        $app = Slim::getInstance();
        $empresaId = $app->id_empresa;
        echo json_encode(['status' => 'success', 'data' => $this->repo->listarCargos($empresaId)]);
    }
}