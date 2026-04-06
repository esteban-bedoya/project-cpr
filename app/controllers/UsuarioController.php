<?php
// Controlador de usuarios (CRUD) para administracion.

require_once __DIR__ . '/../models/User.php';

class UsuarioController
{
    // Se reutiliza en validacion y en la vista.
    private const MENSAJE_MAXIMO_COMISIONADOS = "No se puede crear este comisionado porque ya hay 4 comisionados activos, y ese es el maximo permitido.";

    private function validarVigenciaComisionado($rol, $estado, $vigenciaInicio, $usuarioId = null)
    {
        $errores = [];
        $esComisionado = (int)$rol === 2;
        $estaActivo = (int)$estado === 1;

        // La vigencia solo aplica a comisionados.
        if (!$esComisionado) {
            return [$errores, null];
        }

        if ($vigenciaInicio === '') {
            $errores[] = "El año de inicio de vigencia es obligatorio para comisionados.";
            return [$errores, null];
        }

        if (!ctype_digit($vigenciaInicio)) {
            $errores[] = "La vigencia debe contener un año válido.";
            return [$errores, null];
        }

        $anioInicio = (int)$vigenciaInicio;

        if ($anioInicio < 2000 || $anioInicio > 2100) {
            $errores[] = "El año de vigencia debe estar en un rango válido.";
        }

        // Regla del negocio: maximo 4 activos.
        if ($estaActivo && User::contarComisionadosActivos($usuarioId) >= 4) {
            $errores[] = self::MENSAJE_MAXIMO_COMISIONADOS;
        }

        return [$errores, $anioInicio];
    }

    public function index()
    {
        // Marca la seccion activa en el menu.
        $activePage = 'usuarios';

        // Filtros opcionales desde querystring.
        $filtro_estado = $_GET['filtro_estado'] ?? 'todos';
        $filtro_rol    = $_GET['filtro_rol'] ?? 'todos';
        $filtro_vigencia_inicio = $_GET['filtro_vigencia_inicio'] ?? 'todas';

        // El select de vigencias sale de años reales en BD.
        $usuarios = User::filtrar($filtro_estado, $filtro_rol, $filtro_vigencia_inicio);
        $vigenciasInicio = User::getVigenciasInicio();

        include __DIR__ . '/../views/admin/usuarios.php';
    }


    public function store()
    {
        // Captura datos del formulario de creacion.
        $documento = trim($_POST['documento'] ?? '');
        $username  = trim($_POST['username'] ?? '');
        $password  = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $rol       = $_POST['rol'];
        $correo    = trim($_POST['correo'] ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');
        $estado    = $_POST['estado'] ?? 1;
        $vigenciaInicio = trim($_POST['vigencia_inicio'] ?? '');

        $errores = [];

        if ($username === '') {
            $errores[] = "El nombre completo es obligatorio.";
        }
        if ($documento === '') {
            $errores[] = "El documento es obligatorio.";
        }
        if ($password === '') {
            $errores[] = "La contraseña es obligatoria.";
        }
        if ($passwordConfirm === '') {
            $errores[] = "La confirmación de contraseña es obligatoria.";
        }
        if ($password !== '' && $passwordConfirm !== '' && $password !== $passwordConfirm) {
            $errores[] = "Las contraseñas no coinciden.";
        }
        if ($correo !== '' && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo no es válido.";
        }
        if (User::find($documento)) {
            $errores[] = "El documento ya está registrado.";
        }
        if ($correo !== '' && User::findByEmail($correo)) {
            $errores[] = "El correo ya está registrado.";
        }

        [$erroresVigencia, $anioInicio] = $this->validarVigenciaComisionado($rol, $estado, $vigenciaInicio);
        $errores = array_merge($errores, $erroresVigencia);

        if (!empty($errores)) {
            $_SESSION['error'] = $errores;
            $_SESSION['old'] = [
                'username' => $username,
                'documento' => $documento,
                'correo' => $correo,
                'telefono' => $telefono,
                'rol' => $rol,
                'estado' => $estado,
                'vigencia_inicio' => $vigenciaInicio,
            ];
            // Si falla el tope, el mensaje se muestra fuera del modal.
            $debeCerrarModal = in_array(self::MENSAJE_MAXIMO_COMISIONADOS, $errores, true);
            $urlRedireccion = $debeCerrarModal
                ? "/project-cpr/public/usuarios.php"
                : "/project-cpr/public/usuarios.php?modal=agregar";

            header("Location: " . $urlRedireccion);
            exit;
        }

        // Crea el usuario en BD.
        User::create($documento, $username, $password, $rol, $correo, $telefono, $estado, $anioInicio);

        $_SESSION['success'] = "Usuario creado exitosamente.";
        unset($_SESSION['old']);
        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }


    public function update()
    {
        // Datos de edicion del usuario.
        $id = $_POST['id'];
        $rol       = $_POST['rol'];
        $correo    = $_POST['correo'];
        $telefono  = $_POST['telefono'];
        $estado    = $_POST['estado'];
        $vigenciaInicio = trim($_POST['vigencia_inicio'] ?? '');
        [$erroresVigencia, $anioInicio] = $this->validarVigenciaComisionado($rol, $estado, $vigenciaInicio, $id);

        if (!empty($erroresVigencia)) {
            $_SESSION['error'] = $erroresVigencia;
            header("Location: /project-cpr/public/usuarios.php");
            exit;
        }

        // Actualiza en BD y vuelve al listado.
        User::updateById($id, $rol, $correo, $telefono, $estado, $anioInicio);

        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }


    public function delete()
    {
        // Elimina el usuario por id recibido.
        $id = $_GET['id'];
        User::delete($id);

        header("Location: /project-cpr/public/usuarios.php");
        exit;
    }
}
