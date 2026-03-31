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

    public static function find($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function findByUsername($username)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* Nuevo método necesario para login por correo */
    public static function findByEmail($correo)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ? LIMIT 1");
        $stmt->execute([$correo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($id, $username, $password, $rol, $correo, $telefono, $estado = 1)
    {
        global $pdo;

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO usuarios (id, username, password, rol, correo, telefono, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?)");

        return $stmt->execute([
            $id,
            $username,
            $hashedPassword,
            $rol,
            $correo,
            $telefono,
            $estado
        ]);
    }

    public static function update($id, $username, $rol, $correo, $telefono, $estado, $password = null)
    {
        global $pdo;

        // Si el admin no escribió nueva contraseña → no modificarla
        if ($password === null || trim($password) === '') {
            $stmt = $pdo->prepare("UPDATE usuarios 
                               SET username=?, rol=?, correo=?, telefono=?, estado=?
                               WHERE id=?");

            return $stmt->execute([$username, $rol, $correo, $telefono, $estado, $id]);
        }

        // Si el admin SÍ escribió una nueva contraseña → hashearla y actualizarla
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE usuarios 
                           SET username=?, rol=?, correo=?, telefono=?, estado=?, password=?
                           WHERE id=?");

        return $stmt->execute([$username, $rol, $correo, $telefono, $estado, $hashed, $id]);
    }


    public static function delete($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id=?");
        return $stmt->execute([$id]);
    }
}
