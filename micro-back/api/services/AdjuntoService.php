<?php

namespace App\Services;

use App\Repositories\AdjuntoRepository;
use Exception;

class AdjuntoService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new AdjuntoRepository();
    }

    public function subirArchivo($idTarea, $idUploader, $empresaId, $archivo)
    {
        // 1. Validar errores de subida
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error al subir archivo. Código: " . $archivo['error']);
        }

        // 2. Preparar carpetas (/uploads/ID_EMPRESA)
        $baseDir = __DIR__ . '/../../uploads/' . $empresaId;
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        // 3. Generar nombre único
        $ext = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreUnico = uniqid() . '.' . $ext;
        $rutaFinal = $baseDir . '/' . $nombreUnico;
        
        // 4. Mover archivo
        if (!move_uploaded_file($archivo['tmp_name'], $rutaFinal)) {
            throw new Exception("Error al mover el archivo al servidor.");
        }

        // 5. Determinar tipo
        $tipo = 'DOCUMENTO';
        $imgs = ['jpg','jpeg','png','gif'];
        if (in_array(strtolower($ext), $imgs)) $tipo = 'IMAGEN';

        // 6. Guardar en BD
        $datos = [
            'id_tarea' => $idTarea,
            'id_uploader' => $idUploader,
            'tipo' => $tipo,
            'ruta_archivo' => 'uploads/' . $empresaId . '/' . $nombreUnico, // Ruta relativa pública
            'nombre_original' => $archivo['name'],
            'peso_kb' => round($archivo['size'] / 1024)
        ];

        return $this->repo->create($datos);
    }
    
    public function listar($idTarea) {
        return $this->repo->getByTarea($idTarea);
    }
}