<?php

// ==========================================================
// User.php - Modelo del usuario
// ==========================================================

require_once __DIR__ . '/../../config/db.php';

class User
{

    // ------------------------------------------------------
    // Obtener TODOS los usuarios
    // ------------------------------------------------------
    public static function all()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------
    // Buscar usuario por su DOCUMENTO (identificador real para el admin)
    // ------------------------------------------------------
    public static function find($documento)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE documento = ?");
        $stmt->execute([$documento]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------
    // Buscar usuario por username
    // ------------------------------------------------------
    public static function findByUsername($username)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------
    // Buscar usuario por correo (para login)
    // ------------------------------------------------------
    public static function findByEmail($correo)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ? LIMIT 1");
        $stmt->execute([$correo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ------------------------------------------------------
    // Crear usuario nuevo
    // (IMPORTANTE: NO se envía el id porque ahora es AUTO_INCREMENT)
    // ------------------------------------------------------
    public static function create($documento, $username, $password, $rol, $correo, $telefono, $estado = 1)
    {
        global $pdo;

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO usuarios (documento, username, password, rol, correo, telefono, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $documento,
            $username,
            $hashedPassword,
            $rol,
            $correo,
            $telefono,
            $estado
        ]);
    }

    // ------------------------------------------------------
    // Actualizar usuario existente
    // ------------------------------------------------------
public static function updateById($id, $documento, $username, $rol, $correo, $telefono, $estado, $password = null)
{
    global $pdo;

    // Si NO actualiza contraseña
    if ($password === null || trim($password) === '') {

        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET documento = ?, username = ?, rol = ?, correo = ?, telefono = ?, estado = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $documento,
            $username,
            $rol,
            $correo,
            $telefono,
            $estado,
            $id
        ]);
    }

    // Si sí cambia contraseña
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        UPDATE usuarios
        SET documento = ?, username = ?, rol = ?, correo = ?, telefono = ?, estado = ?, password = ?
        WHERE id = ?
    ");

    return $stmt->execute([
        $documento,
        $username,
        $rol,
        $correo,
        $telefono,
        $estado,
        $hashed,
        $id
    ]);
}


    // ------------------------------------------------------
    // Eliminar usuario por DOCUMENTO
    // ------------------------------------------------------
    public static function delete($documento)
    {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE documento = ?");
        return $stmt->execute([$documento]);
    }
}
