<?php

require_once __DIR__ . '/../../config/db.php';

class User
{
    public static function all()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($documento)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE documento = ?");
        $stmt->execute([$documento]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByEmail($correo)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ? LIMIT 1");
        $stmt->execute([$correo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($documento, $username, $password, $rol, $correo, $telefono, $estado = 1)
    {
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

    public static function updateById($id, $documento, $username, $rol, $correo, $telefono, $estado, $password = null)
    {
        global $pdo;

        if ($password === null || trim($password) === '') {
            $stmt = $pdo->prepare("
                UPDATE usuarios
                SET documento = ?, username = ?, rol = ?, correo = ?, telefono = ?, estado = ?
                WHERE id = ?
            ");
            return $stmt->execute([$documento, $username, $rol, $correo, $telefono, $estado, $id]);
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET documento = ?, username = ?, rol = ?, correo = ?, telefono = ?, estado = ?, password = ?
            WHERE id = ?
        ");

        return $stmt->execute([$documento, $username, $rol, $correo, $telefono, $estado, $hashed, $id]);
    }

    public static function delete($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function filtrar($estado, $rol)
{
    global $pdo;

    // Base SQL
    $sql = "SELECT * FROM usuarios WHERE 1 = 1";
    $params = [];

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


}
