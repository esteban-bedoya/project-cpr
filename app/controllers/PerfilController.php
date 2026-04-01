<?php
// Controlador de perfil: datos del usuario y catalogo de tipos de proceso (admin).

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/TipoProceso.php';
require_once __DIR__ . '/../models/Caso.php';

class PerfilController
{
    public function index()
    {
        // Marca el menu activo en la vista.
        $activePage = 'perfil';

        $tiposProceso = [];
        $tiposCaso = [];
        $procesoSeleccionado = null;

        // Solo el admin carga catalogos para gestion de procesos.
        if (isset($_SESSION['user']) && $_SESSION['user']['rol'] == 1) {
            $tiposProceso = TipoProceso::all();

            $procesoId = $_GET['proceso_id'] ?? null;
            if ($procesoId) {
                $procesoSeleccionado = TipoProceso::find($procesoId);
            }
        }

        // Vista según rol
        if ($_SESSION['user']['rol'] == 1) {
            include __DIR__ . '/../views/admin/perfil.php';
        } else {
            include __DIR__ . '/../views/comisionado/perfil.php';
        }
    }

    public function update()
{
    // Actualiza correo y/o contraseña del usuario logueado.
    $idUsuario = $_SESSION['user']['id'];

    $nuevoCorreo   = trim($_POST['nuevo_correo'] ?? '');
    $confirmCorreo = trim($_POST['confirm_correo'] ?? '');

    $nuevaContra   = trim($_POST['nueva_contra'] ?? '');
    $confirmContra = trim($_POST['confirm_contra'] ?? '');

    $actualContra  = $_POST['actual_contra'] ?? '';

    $usuario = User::findById($idUsuario);

    // 1️ Validar contraseña actual
    if (!password_verify($actualContra, $usuario['password'])) {
        $_SESSION['error'] = "La contraseña actual es incorrecta.";
        header("Location: /project-cpr/public/perfil.php");
        exit;
    }

    $cambioCorreo = false;
    $cambioContra = false;

    // 2️ Validar y preparar correo
    if ($nuevoCorreo !== '') {
        if ($nuevoCorreo !== $confirmCorreo) {
            $_SESSION['error'] = "Los correos no coinciden.";
            header("Location: /project-cpr/public/perfil.php");
            exit;
        }
        $cambioCorreo = true;
    }

    // 3️ Validar y preparar contraseña
    if ($nuevaContra !== '') {
        if ($nuevaContra !== $confirmContra) {
            $_SESSION['error'] = "Las contraseñas no coinciden.";
            header("Location: /project-cpr/public/perfil.php");
            exit;
        }
        $cambioContra = true;
    }

    // 4️ Si no cambió nada
    if (!$cambioCorreo && !$cambioContra) {
        $_SESSION['error'] = "No se realizaron cambios.";
        header("Location: /project-cpr/public/perfil.php");
        exit;
    }

    // 5️ Actualizar
    User::updatePerfil(
        $idUsuario,
        $cambioCorreo ? $nuevoCorreo : $usuario['correo'],
        $cambioContra ? $nuevaContra : null
    );

    // 6️ Mensaje final claro
    if ($cambioCorreo && $cambioContra) {
        $_SESSION['success'] = "Correo y contraseña actualizados correctamente.";
    } elseif ($cambioCorreo) {
        $_SESSION['success'] = "Correo actualizado correctamente.";
    } elseif ($cambioContra) {
        $_SESSION['success'] = "Contraseña actualizada correctamente.";
    }

    header("Location: /project-cpr/public/perfil.php");
    exit;
}

    public function guardarProceso()
    {
        // Solo el admin puede crear/editar tipos de proceso.
        if (!isset($_SESSION['logged']) || $_SESSION['user']['rol'] != 1) {
            header("Location: /project-cpr/public/login.php");
            exit;
        }

        $id = $_POST['proceso_id'] ?? '';
        $nombre = trim($_POST['proceso_nombre'] ?? '');
        $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;
        if ($nombre === '') {
            $_SESSION['error'] = "Debe ingresar el nombre del proceso.";
            header("Location: /project-cpr/public/perfil.php");
            exit;
        }

        // Si hay id: actualiza, si no: crea.
        if ($id) {
            TipoProceso::update($id, $nombre, $estado);
            $_SESSION['success'] = "Proceso actualizado correctamente.";
        } else {
            TipoProceso::create($nombre, $estado);
            $_SESSION['success'] = "Proceso creado correctamente.";
        }

        header("Location: /project-cpr/public/perfil.php");
        exit;
    }

    public function eliminarProceso()
    {
        // Elimina un tipo de proceso si no tiene casos asignados.
        if (!isset($_SESSION['logged']) || $_SESSION['user']['rol'] != 1) {
            header("Location: /project-cpr/public/login.php");
            exit;
        }

        $id = $_POST['proceso_id'] ?? '';
        if (!$id) {
            $_SESSION['error'] = "Debe seleccionar un proceso para eliminar.";
            header("Location: /project-cpr/public/perfil.php");
            exit;
        }

        $count = TipoProceso::countCasosAsignados($id);
        if ($count > 0) {
            $casos = TipoProceso::getCasosAsignados($id);
            $numeros = array_map(fn($c) => $c['numero_caso'], $casos);
            $_SESSION['error'] = "No se puede eliminar. Casos asignados: " . implode(', ', $numeros);
            header("Location: /project-cpr/public/perfil.php");
            exit;
        }

        TipoProceso::delete($id);
        $_SESSION['success'] = "Proceso eliminado correctamente.";
        header("Location: /project-cpr/public/perfil.php");
        exit;
    }
}
