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

    public static function create($documento, $username, $password, $rol, $correo, $telefono, $estado = 1)
    {
        // Inserta un nuevo usuario con password hasheada.
        global $pdo;

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO usuarios (documento, username, password, rol, correo, telefono, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $documento, $username, $hashed, $rol, $correo, $telefono, $estado
        ]);
    }

    public static function updateById($id, $rol, $correo, $telefono, $estado, $password = null)
    {
        // Actualiza usuario; la contraseña es opcional.
        global $pdo;

        if ($password === null || trim($password) === '') {
            $stmt = $pdo->prepare("
                UPDATE usuarios
                SET rol = ?, correo = ?, telefono = ?, estado = ?
                WHERE id = ?
            ");
            return $stmt->execute([$rol, $correo, $telefono, $estado, $id]);
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET rol = ?, correo = ?, telefono = ?, estado = ?, password = ?
            WHERE id = ?
        ");

        return $stmt->execute([$rol, $correo, $telefono, $estado, $hashed, $id]);
    }

    public static function delete($id)
    {
        // Elimina usuario por id.
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function filtrar($estado, $rol)
{
    // Filtrado dinamico por estado y rol (usado en admin).
    global $pdo;

    // Base SQL
    $sql = "SELECT * FROM usuarios WHERE 1 = 1";
    $params = [];

    // Oculta usuario del sistema en listados admin
    $sql .= " AND NOT (username = ? AND documento = ?)";
    $params[] = 'Sistema';
    $params[] = 'SYSTEM-000';

    // Filtrar por estado
    if ($estado !== 'todos') {
        if ($estado === 'activos') {
            $sql .= " AND estado = 1";
        } elseif ($estado === 'inactivos') {
            $sql .= " AND estado = 2";
        }
    }

    // Filtrar por rol
    if ($rol !== 'todos') {
        $sql .= " AND rol = ?";
        $params[] = $rol;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

}
