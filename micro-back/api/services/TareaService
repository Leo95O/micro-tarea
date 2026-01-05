<?php

namespace App\Services;

use App\Repositories\TareaRepository;
use Exception;

class TareaService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new TareaRepository();
    }

    public function crearTarea($data, $usuarioId, $empresaId)
    {
        // REGLA 1: Validación de Tiempos (Mínimo 15 mins)
        $inicio = strtotime($data['fecha_inicio']);
        $fin = strtotime($data['fecha_fin']);
        $diffMinutos = ($fin - $inicio) / 60;

        if ($diffMinutos < 15) {
            throw new Exception("La tarea debe durar al menos 15 minutos.");
        }

        // Preparar datos
        $data['id_creador'] = $usuarioId;
        $data['id_empresa'] = $empresaId;
        // Si no viene asignado, es NULL (Pool)
        $data['id_asignado'] = !empty($data['id_asignado']) ? $data['id_asignado'] : null;
        $data['nivel_minimo_requerido'] = $data['nivel_minimo'] ?? 10;

        return $this->repository->create($data);
    }

    public function tomarTareaDelPool($tareaId, $usuarioId, $empresaId, $nivelUsuario)
    {
        $tarea = $this->repository->findById($tareaId, $empresaId);
        if (!$tarea) {
            throw new Exception("Tarea no encontrada.");
        }

        // REGLA 2: Nivel Jerárquico
        if ($nivelUsuario < $tarea['nivel_minimo_requerido']) {
            throw new Exception("Nivel insuficiente para tomar esta tarea.");
        }

        // Intento Atómico
        $exito = $this->repository->assignFromPool($tareaId, $usuarioId, $empresaId);
        
        if (!$exito) {
            throw new Exception("¡Alguien más acaba de tomar esta tarea!");
        }

        return true;
    }

    public function evaluarTarea($tareaId, $accion, $data, $empresaId)
    {
        // Acciones: 'APROBAR', 'CORREGIR', 'FALLAR'
        $tarea = $this->repository->findById($tareaId, $empresaId);
        if (!$tarea) throw new Exception("Tarea no encontrada.");

        if ($accion === 'CORREGIR') {
            // REGLA 3: Las 3 Vidas
            if ($tarea['intentos_usados'] >= $tarea['limite_intentos']) {
                throw new Exception("Límite de intentos alcanzado (3/3). Debes APROBAR o FALLAR la tarea.");
            }
            // Consumir vida
            $this->repository->consumeLife($tareaId);
            // Si mandaron nueva fecha (prórroga), actualizar
            if (!empty($data['nueva_fecha_fin'])) {
                $this->repository->extendTime($tareaId, $data['nueva_fecha_fin']);
            }
            return "Tarea enviada a corrección. Vidas restantes: " . (2 - $tarea['intentos_usados']);
        }

        if ($accion === 'APROBAR') {
            $nota = $data['calificacion'] ?? 20;
            $this->repository->finalize($tareaId, 'VALIDADA', 'EXITO', $nota);
            return "Tarea aprobada con éxito.";
        }

        if ($accion === 'FALLAR') {
            $motivo = $data['motivo'] ?? 'INCAPACIDAD'; // O 'TIEMPO_AGOTADO'
            $this->repository->finalize($tareaId, 'FALLIDA', $motivo, 0);
            return "Tarea marcada como fallida.";
        }

        throw new Exception("Acción no válida.");
    }
}