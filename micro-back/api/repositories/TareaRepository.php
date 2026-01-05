<?php

namespace App\Repositories;

use Core\DB;
use PDO;

class TareaRepository
{
    private $db;

    public function __construct()
    {
        $this->db = DB::getInstance();
    }

    // CREAR TAREA (SaaS: Vinculada a id_empresa)
    public function create($data)
    {
        $sql = "INSERT INTO tareas (
                    id_empresa, id_sucursal, id_area, id_creador, id_asignado,
                    nivel_minimo_requerido, titulo, descripcion, prioridad,
                    fecha_inicio, fecha_fin_original, fecha_fin_actual,
                    estado_ejecucion, estado_validacion
                ) VALUES (
                    :id_empresa, :id_sucursal, :id_area, :id_creador, :id_asignado,
                    :nivel_minimo_requerido, :titulo, :descripcion, :prioridad,
                    :fecha_inicio, :fecha_fin_original, :fecha_fin_original, -- Al inicio, fin actual = original
                    'PROGRAMADA', 'POR_VALIDAR'
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_empresa' => $data['id_empresa'],
            ':id_sucursal' => $data['id_sucursal'],
            ':id_area' => $data['id_area'],
            ':id_creador' => $data['id_creador'],
            ':id_asignado' => $data['id_asignado'], // Puede ser NULL (Pool)
            ':nivel_minimo_requerido' => $data['nivel_minimo_requerido'],
            ':titulo' => $data['titulo'],
            ':descripcion' => $data['descripcion'],
            ':prioridad' => $data['prioridad'],
            ':fecha_inicio' => $data['fecha_inicio'],
            ':fecha_fin_original' => $data['fecha_fin']
        ]);

        return $this->db->lastInsertId();
    }

    // BUSCAR POR ID (Protegido por Empresa)
    public function findById($id, $empresaId)
    {
        $stmt = $this->db->prepare("SELECT * FROM tareas WHERE id = :id AND id_empresa = :empresaId AND activo = 1");
        $stmt->execute([':id' => $id, ':empresaId' => $empresaId]);
        return $stmt->fetch();
    }

    // POOL ATÓMICO: Asignar solo si está libre
    public function assignFromPool($tareaId, $usuarioId, $empresaId)
    {
        // El truco: WHERE id_asignado IS NULL. Si alguien ganó el click, esto devolverá 0 filas afectadas.
        $sql = "UPDATE tareas 
                SET id_asignado = :usuarioId, 
                    estado_ejecucion = 'EN_PROGRESO',
                    fecha_inicio = NOW() -- Se actualiza al momento real de toma
                WHERE id = :id 
                  AND id_empresa = :empresaId 
                  AND id_asignado IS NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuarioId' => $usuarioId,
            ':id' => $tareaId,
            ':empresaId' => $empresaId
        ]);

        return $stmt->rowCount() > 0; // True si tuvo éxito, False si ya estaba tomada
    }

    // LOGICA DE 3 VIDAS: Consumir un intento (Rechazo/Corrección)
    public function consumeLife($id)
    {
        $sql = "UPDATE tareas 
                SET intentos_usados = intentos_usados + 1,
                    estado_validacion = 'RECHAZADA'
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    // FINALIZAR TAREA (Validación Exitosa o Fallida)
    public function finalize($id, $estadoValidacion, $motivoCierre, $calificacion = null)
    {
        $sql = "UPDATE tareas 
                SET estado_ejecucion = 'COMPLETADA',
                    estado_validacion = :val,
                    motivo_cierre = :motivo,
                    calificacion = :nota
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':val' => $estadoValidacion,
            ':motivo' => $motivoCierre,
            ':nota' => $calificacion,
            ':id' => $id
        ]);
    }

    // ACTUALIZAR FECHAS (Prórrogas)
    public function extendTime($id, $nuevaFecha)
    {
        $sql = "UPDATE tareas 
                SET fecha_fin_actual = :fecha 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':fecha' => $nuevaFecha, ':id' => $id]);
    }
}