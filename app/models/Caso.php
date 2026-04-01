<?php
// Modelo Caso: encapsula consultas y cambios relacionados con la entidad "casos".

class Caso
{
    private static function db()
    {
        // Conexion PDO simple reutilizable (patron singleton basico).
        static $db = null;
        if ($db === null) {
            $db = new PDO("mysql:host=localhost;dbname=project-cpr;charset=utf8", "root", "");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $db;
    }

    // ===============================
    // OBTENER TODOS LOS CASOS
    // ===============================
    public static function all()
    {
        // Lista casos con datos de usuario y catalogos (joins).
        $sql = "SELECT c.*, u.username AS creado_por_nombre, tc.nombre AS tipo_caso_nombre, tp.nombre AS tipo_proceso_nombre
                FROM casos c
                JOIN usuarios u ON u.id = c.creado_por
                JOIN tipos_caso tc ON tc.id = c.tipo_caso_id
                JOIN tipos_proceso tp ON tp.id = c.tipo_proceso_id
                ORDER BY c.id DESC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===============================
    // BUSCAR UN CASO POR ID
    // ===============================
    public static function find($id)
    {
        // Busca un caso y sus relaciones principales.
        $sql = "SELECT c.*, u.username AS creado_por_nombre, tc.nombre AS tipo_caso_nombre, tp.nombre AS tipo_proceso_nombre
                FROM casos c
                JOIN usuarios u ON u.id = c.creado_por
                JOIN tipos_caso tc ON tc.id = c.tipo_caso_id
                JOIN tipos_proceso tp ON tp.id = c.tipo_proceso_id
                WHERE c.id = ?";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ===============================
    // CREAR CASO
    // ===============================
    public static function create($data)
    {
        // Inserta un nuevo caso.
        $sql = "
        INSERT INTO casos
        (
            tipo_caso_id,
            tipo_proceso_id,
            demandante_nombre,
            demandante_documento,
            demandante_contacto,
            asunto,
            detalles,
            estado,
            creado_por,
            asignado_a
        )
        VALUES
        (
            :tipo_caso_id,
            :tipo_proceso_id,
            :demandante_nombre,
            :demandante_documento,
            :demandante_contacto,
            :asunto,
            :detalles,
            :estado,
            :creado_por,
            :asignado_a
        )
    ";

        $stmt = self::db()->prepare($sql);

        return $stmt->execute([
            ':tipo_caso_id'         => $data['tipo_caso_id'],
            ':tipo_proceso_id'      => $data['tipo_proceso_id'],
            ':demandante_nombre'    => $data['demandante_nombre'],
            ':demandante_documento' => $data['demandante_documento'], // campo opcional en el formulario
            ':demandante_contacto'  => $data['demandante_contacto'],
            ':asunto'               => $data['asunto'],
            ':detalles'             => $data['detalles'],
            ':estado'               => $data['estado'] ?? 'No atendido',
            ':creado_por'           => $data['creado_por'],
            ':asignado_a'           => $data['asignado_a']
        ]);
    }


    // ===============================
    // ACTUALIZAR CASO
    // ===============================
    public static function update($id, $data)
    {
        // Actualiza campos generales del caso.
        $sql = "UPDATE casos SET 
                    tipo_caso_id = :tipo_caso_id,
                    tipo_proceso_id = :tipo_proceso_id,
                    demandante_nombre = :demandante_nombre,
                    demandante_contacto = :demandante_contacto,
                    asunto = :asunto,
                    detalles = :detalles,
                    estado = :estado
                WHERE id = :id";
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([
            ':tipo_caso_id'       => $data['tipo_caso_id'] ?? null,
            ':tipo_proceso_id'    => $data['tipo_proceso_id'] ?? null,
            ':demandante_nombre'  => $data['demandante_nombre'] ?? null,
            ':demandante_contacto' => $data['demandante_contacto'] ?? null,
            ':asunto'             => $data['asunto'] ?? null,
            ':detalles'           => $data['detalles'] ?? null,
            ':estado'             => $data['estado'] ?? null,
            ':id'                 => $id
        ]);
    }

    // ===============================
    // ELIMINAR CASO
    // ===============================
    public static function delete($id)
    {
        // Elimina un caso por id.
        $sql = "DELETE FROM casos WHERE id = ?";
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([$id]);
    }

    // ===============================
    // TIPOS DE CASO
    // ===============================
    public static function getTiposCaso()
    {
        // Catalogo de tipos de caso.
        $stmt = self::db()->prepare("SELECT * FROM tipos_caso");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===============================
    // TIPOS DE PROCESO
    // ===============================
    public static function getTiposProceso()
    {
        // Catalogo de tipos de proceso.
        $stmt = self::db()->prepare("SELECT * FROM tipos_proceso");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===============================
    // HISTORIAL DE ESTADO
    // ===============================
    public static function getHistorial($caso_id)
    {
        // Historial de cambios/estados del caso.
        $sql = "SELECT h.*, u.username 
                FROM casos_historial_estado h
                JOIN usuarios u ON u.id = h.usuario_id
                WHERE h.caso_id = ?
                ORDER BY h.fecha ASC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$caso_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===============================
    // MENSAJES DEL CASO
    // ===============================
    public static function getMensajes($caso_id)
    {
        // Mensajes y archivos adjuntos del caso.
        $sql = "SELECT m.*, u.username 
                FROM casos_mensajes m
                JOIN usuarios u ON u.id = m.usuario_id
                WHERE m.caso_id = ?
                ORDER BY m.fecha ASC";
        $stmt = self::db()->prepare($sql);
        $stmt->execute([$caso_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===============================
    // OBTENER CASOS ASIGNADOS A UN COMISIONADO
    // ===============================
    public static function getByComisionado($comisionado_id)
    {
        // Casos asignados a un comisionado especifico.
        $sql = "
        SELECT c.*, tc.nombre AS tipo_caso_nombre, tp.nombre AS tipo_proceso_nombre
        FROM casos c
        JOIN tipos_caso tc ON tc.id = c.tipo_caso_id
        JOIN tipos_proceso tp ON tp.id = c.tipo_proceso_id
        WHERE c.asignado_a = :comisionado_id
        ORDER BY c.id DESC
    ";

        $stmt = self::db()->prepare($sql);
        $stmt->execute([':comisionado_id' => $comisionado_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear asignación en casos_asignaciones
    public static function crearAsignacion($data)
    {
        // Registra una asignacion en tabla historica.
        $sql = "INSERT INTO casos_asignaciones (caso_id, comisionado_id, asignado_por) VALUES (:caso_id, :comisionado_id, :asignado_por)";
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([
            ':caso_id' => $data['caso_id'],
            ':comisionado_id' => $data['comisionado_id'],
            ':asignado_por' => $data['asignado_por']
        ]);
    }

    // Obtener comisionados activos
    public static function getComisionadosActivos()
    {
        // Lista comisionados activos para selects.
        $sql = "SELECT id, username FROM usuarios WHERE rol = 2 AND estado = 1 ORDER BY username";
        $stmt = self::db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Guardar un cambio en el historial (solo descripción)
    public static function guardarHistorial($data)
    {
        // Inserta un registro de historial textual.
        $sql = "INSERT INTO casos_historial_estado (caso_id, usuario_id, descripcion) 
            VALUES (:caso_id, :usuario_id, :descripcion)";
        $stmt = self::db()->prepare($sql);
        return $stmt->execute([
            ':caso_id' => $data['caso_id'],
            ':usuario_id' => $data['usuario_id'],
            ':descripcion' => $data['descripcion']
        ]);
    }

    // Obtener usuario por ID
    public static function getUsuario($id)
    {
        // Obtiene datos basicos del usuario (para historial/assignaciones).
        $stmt = self::db()->prepare("SELECT id, username, estado FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener tipo de proceso
    public static function getTipoProceso($id)
    {
        // Consulta un tipo de proceso puntual.
        $stmt = self::db()->prepare("SELECT * FROM tipos_proceso WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener tipo de caso
    public static function getTipoCaso($id)
    {
        // Consulta un tipo de caso puntual.
        $stmt = self::db()->prepare("SELECT * FROM tipos_caso WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ===============================
    // GUARDAR MENSAJE DEL CASO
    // ===============================
    public static function guardarMensaje($data)
    {
        // Guarda mensaje y archivo asociado (si aplica).
        $sql = "INSERT INTO casos_mensajes 
            (caso_id, usuario_id, mensaje, archivo)
            VALUES (:caso_id, :usuario_id, :mensaje, :archivo)";

        $stmt = self::db()->prepare($sql);
        return $stmt->execute([
            ':caso_id'   => $data['caso_id'],
            ':usuario_id' => $data['usuario_id'],
            ':mensaje'   => $data['mensaje'],
            ':archivo'   => $data['archivo']
        ]);
    }

    public static function updateDetalle($id, $data)
    {
        // Actualiza campos especificos desde la vista detalle.
        $sql = "UPDATE casos SET 
        asignado_a = :asignado_a,
        estado = :estado,
        tipo_proceso_id = :tipo_proceso_id,
        tipo_caso_id = :tipo_caso_id
        WHERE id = :id";

        $stmt = self::db()->prepare($sql);
        return $stmt->execute([
            ':asignado_a' => $data['asignado_a'],
            ':estado' => $data['estado'],
            ':tipo_proceso_id' => $data['tipo_proceso_id'],
            ':tipo_caso_id' => $data['tipo_caso_id'],
            ':id' => $id
        ]);
    }
}
