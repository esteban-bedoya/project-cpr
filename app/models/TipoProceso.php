<?php
// Modelo TipoProceso: gestiona catalogo de procesos.

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

    public static function all()
    {
        // Lista tipos de proceso (independientes).
        $sql = "SELECT * FROM tipos_proceso ORDER BY nombre";
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

    public static function create($nombre, $estado = 1)
    {
        // Crea un nuevo tipo de proceso.
        $stmt = self::db()->prepare("INSERT INTO tipos_proceso (nombre, estado) VALUES (?, ?)");
        return $stmt->execute([$nombre, $estado]);
    }

    public static function update($id, $nombre, $estado = 1)
    {
        // Actualiza un tipo de proceso.
        $stmt = self::db()->prepare("UPDATE tipos_proceso SET nombre = ?, estado = ? WHERE id = ?");
        return $stmt->execute([$nombre, $estado, $id]);
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
