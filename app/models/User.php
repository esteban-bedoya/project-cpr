<?php
// Modelo User: operaciones CRUD y consultas de usuarios.

require_once __DIR__ . '/../../config/db.php';

class User
{
    public static function all()
    {
        // Retorna todos los usuarios.
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($documento)
    {
        // Busca usuario por documento.
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE documento = ?");
        $stmt->execute([$documento]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByEmail($correo)
    {
        // Busca usuario por correo (login).
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ? LIMIT 1");
        $stmt->execute([$correo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($documento, $username, $password, $rol, $correo, $telefono, $estado = 1, $vigenciaInicio = null)
    {
        // Inserta un nuevo usuario con password hasheada.
        global $pdo;

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO usuarios (documento, username, password, rol, correo, telefono, estado, vigencia_inicio)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $documento, $username, $hashed, $rol, $correo, $telefono, $estado, $vigenciaInicio
        ]);
    }

    public static function updateById($id, $rol, $correo, $telefono, $estado, $vigenciaInicio = null, $password = null)
    {
        // Actualiza usuario; la contraseña es opcional.
        global $pdo;

        if ($password === null || trim($password) === '') {
            $stmt = $pdo->prepare("
                UPDATE usuarios
                SET rol = ?, correo = ?, telefono = ?, estado = ?, vigencia_inicio = ?
                WHERE id = ?
            ");
            return $stmt->execute([$rol, $correo, $telefono, $estado, $vigenciaInicio, $id]);
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET rol = ?, correo = ?, telefono = ?, estado = ?, vigencia_inicio = ?, password = ?
            WHERE id = ?
        ");

        return $stmt->execute([$rol, $correo, $telefono, $estado, $vigenciaInicio, $hashed, $id]);
    }

    public static function delete($id)
    {
        // Elimina usuario por id.
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function filtrar($estado, $rol, $vigenciaInicio = 'todas')
    {
        // Filtrado dinamico por estado, rol y vigencia (usado en admin).
        global $pdo;

        $sql = "SELECT * FROM usuarios WHERE 1 = 1";
        $params = [];

        // Oculta usuario del sistema en listados admin
        $sql .= " AND NOT (username = ? AND documento = ?)";
        $params[] = 'Sistema';
        $params[] = 'SYSTEM-000';

        if ($estado !== 'todos') {
            if ($estado === 'activos') {
                $sql .= " AND estado = 1";
            } elseif ($estado === 'inactivos') {
                $sql .= " AND estado = 2";
            }
        }

        if ($rol !== 'todos') {
            $sql .= " AND rol = ?";
            $params[] = $rol;
        }

        if ($vigenciaInicio !== 'todas' && ctype_digit((string)$vigenciaInicio)) {
            $sql .= " AND vigencia_inicio = ?";
            $params[] = (int)$vigenciaInicio;
        }

        $sql .= " ORDER BY username ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getVigenciasInicio()
    {
        // Retorna las vigencias existentes para el filtro.
        global $pdo;

        $stmt = $pdo->query("
            SELECT DISTINCT vigencia_inicio
            FROM usuarios
            WHERE vigencia_inicio IS NOT NULL
            ORDER BY vigencia_inicio DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

public static function findById($id)
{
    // Busca usuario por id (uso interno).
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public static function findByUsername($username)
{
    // Busca usuario por username (para usuario "Sistema").
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public static function updatePerfil($id, $correo, $password = null)
{
    // Actualiza datos de perfil (correo y opcionalmente password).
    global $pdo;

    if ($password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            UPDATE usuarios SET correo = ?, password = ? WHERE id = ?
        ");
        return $stmt->execute([$correo, $hashed, $id]);
    }

    $stmt = $pdo->prepare("
        UPDATE usuarios SET correo = ? WHERE id = ?
    ");
    return $stmt->execute([$correo, $id]);
}

public static function getComisionadosAll()
{
    // Lista todos los comisionados (activos e inactivos).
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE rol = 2");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public static function contarComisionadosActivos($ignorarId = null)
{
    global $pdo;

    $sql = "SELECT COUNT(*) FROM usuarios WHERE rol = 2 AND estado = 1";
    $params = [];

    if ($ignorarId !== null) {
        $sql .= " AND id <> ?";
        $params[] = $ignorarId;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return (int)$stmt->fetchColumn();
}

public static function saveRememberToken($id, $token)
{
    // Guarda un token temporal para recuperacion de contraseña.
    global $pdo;
    $stmt = $pdo->prepare("UPDATE usuarios SET remember_token = ? WHERE id = ?");
    return $stmt->execute([$token, $id]);
}

public static function findByRememberToken($token)
{
    // Busca usuario por token de recuperacion.
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE remember_token = ? LIMIT 1");
    $stmt->execute([$token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public static function clearRememberToken($id)
{
    // Limpia el token despues de usarlo.
    global $pdo;
    $stmt = $pdo->prepare("UPDATE usuarios SET remember_token = NULL WHERE id = ?");
    return $stmt->execute([$id]);
}

public static function updatePasswordById($id, $password)
{
    // Actualiza solo la contraseña del usuario.
    global $pdo;
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
    return $stmt->execute([$hashed, $id]);
}

}
