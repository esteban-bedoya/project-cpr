<?php
// Controlador de tipos de proceso: gestion exclusiva del administrador.

require_once __DIR__ . '/../models/TipoProceso.php';

class TipoProcesoController
{
    private function validarAccesoAdmin()
    {
        // Esta vista no depende del perfil. Se separó para que quede más claro
        // que es una configuración del sistema y no un dato personal del usuario.
        if (!isset($_SESSION['logged']) || ($_SESSION['user']['rol'] ?? null) != 1) {
            header("Location: /project-cpr/public/login.php");
            exit;
        }
    }

    public function index()
    {
        $this->validarAccesoAdmin();

        $activePage = 'tipos_proceso';
        $tiposProceso = TipoProceso::all();
        $procesoSeleccionado = null;

        // Si llega un id por GET, el formulario carga en modo edición.
        $procesoId = $_GET['proceso_id'] ?? null;
        if ($procesoId) {
            $procesoSeleccionado = TipoProceso::find($procesoId);
        }

        include __DIR__ . '/../views/admin/tipos_proceso.php';
    }

    public function guardar()
    {
        $this->validarAccesoAdmin();

        $id = $_POST['proceso_id'] ?? '';
        $nombre = trim($_POST['proceso_nombre'] ?? '');
        $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;

        if ($nombre === '') {
            $_SESSION['error'] = "Debe ingresar el nombre del proceso.";
            header("Location: /project-cpr/public/tipos_proceso.php");
            exit;
        }

        // Con id actualiza; sin id crea uno nuevo.
        if ($id) {
            TipoProceso::update($id, $nombre, $estado);
            $_SESSION['success'] = "Proceso actualizado correctamente.";
        } else {
            TipoProceso::create($nombre, $estado);
            $_SESSION['success'] = "Proceso creado correctamente.";
        }

        header("Location: /project-cpr/public/tipos_proceso.php");
        exit;
    }

    public function eliminar()
    {
        $this->validarAccesoAdmin();

        $id = $_POST['proceso_id'] ?? '';
        if (!$id) {
            $_SESSION['error'] = "Debe seleccionar un proceso para eliminar.";
            header("Location: /project-cpr/public/tipos_proceso.php");
            exit;
        }

        // No se deja eliminar un proceso si ya está ligado a casos,
        // porque eso rompería la consistencia del historial.
        $cantidadCasosAsignados = TipoProceso::countCasosAsignados($id);
        if ($cantidadCasosAsignados > 0) {
            $casosAsignados = TipoProceso::getCasosAsignados($id);
            $numerosCaso = array_map(fn($caso) => $caso['numero_caso'], $casosAsignados);
            $_SESSION['error'] = "No se puede eliminar. Casos asignados: " . implode(', ', $numerosCaso);
            header("Location: /project-cpr/public/tipos_proceso.php");
            exit;
        }

        TipoProceso::delete($id);
        $_SESSION['success'] = "Proceso eliminado correctamente.";
        header("Location: /project-cpr/public/tipos_proceso.php");
        exit;
    }
}
