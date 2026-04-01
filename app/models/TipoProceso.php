<?php
// Modelo TipoProceso: gestiona catalogo de procesos y sus relaciones.

class TipoProceso
{
    private static function db()
    {
        // Conexion PDO.
        static $db = null;
        if ($db === null) {
            $db = new PDO("mysql:host=localhost;dbname=project-cpr;charset=utf8", "root", "");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $db;
    }

    public static function allWithTipoCaso()
    {
        // Lista tipos de proceso con su tipo de caso asociado.
        $sql = "SELECT tp.*, tc.nombre AS tipo_caso_nombre
                FROM tipos_proceso tp
                JOIN tipos_caso tc ON tc.id = tp.tipo_caso_id
                ORDER BY tp.nombre";
        $stmt = self::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id)
    {
        // Busca un tipo de proceso por id.
        $stmt = self::db()->prepare("SELECT * FROM tipos_proceso WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($nombre, $tipo_caso_id)
    {
        // Crea un nuevo tipo de proceso.
        $stmt = self::db()->prepare("INSERT INTO tipos_proceso (nombre, tipo_caso_id) VALUES (?, ?)");
        return $stmt->execute([$nombre, $tipo_caso_id]);
    }

    public static function update($id, $nombre, $tipo_caso_id)
    {
        // Actualiza un tipo de proceso.
        $stmt = self::db()->prepare("UPDATE tipos_proceso SET nombre = ?, tipo_caso_id = ? WHERE id = ?");
        return $stmt->execute([$nombre, $tipo_caso_id, $id]);
    }

    public static function delete($id)
    {
        // Elimina un tipo de proceso.
        $stmt = self::db()->prepare("DELETE FROM tipos_proceso WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function countCasosAsignados($tipo_proceso_id)
    {
        // Cuenta casos asociados para evitar eliminar si hay dependencias.
        $stmt = self::db()->prepare("SELECT COUNT(*) FROM casos WHERE tipo_proceso_id = ?");
        $stmt->execute([$tipo_proceso_id]);
        return (int)$stmt->fetchColumn();
    }

    public static function getCasosAsignados($tipo_proceso_id)
    {
        // Lista casos asociados (para mensaje de bloqueo).
        $stmt = self::db()->prepare("SELECT id, numero_caso FROM casos WHERE tipo_proceso_id = ? ORDER BY id DESC");
        $stmt->execute([$tipo_proceso_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
