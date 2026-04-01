<?php
require_once __DIR__ . '/../models/Caso.php';

class CasoController
{
    // ===============================
    // MOSTRAR TODOS LOS CASOS
    // ===============================
    public function index()
    {
        session_start();
        if (!isset($_SESSION['logged'])) header("Location: /project-cpr/public/login.php");

        $rol = $_SESSION['user']['rol'];
        if (!in_array($rol, [1, 2])) header("Location: /project-cpr/public/login.php");

        $casos = Caso::all();

        if ($rol == 1) {
            require __DIR__ . '/../views/admin/casos.php';
        } else {
            require __DIR__ . '/../views/comisionado/casos.php';
        }
    }

    // ===============================
    // MOSTRAR UN CASO
    // ===============================
    public function show($id)
    {
        session_start();
        if (!isset($_SESSION['logged'])) header("Location: /project-cpr/public/login.php");

        $rol = $_SESSION['user']['rol'];
        if (!in_array($rol, [1, 2])) header("Location: /project-cpr/public/login.php");

        $caso = Caso::find($id);
        if (!$caso) header("Location: /project-cpr/casos");

        $tiposCaso = Caso::getTiposCaso();
        $tiposProceso = Caso::getTiposProceso();
        $historial = Caso::getHistorial($id);
        $mensajes = Caso::getMensajes($id);

        // ===============================
        // CARGAR VISTA SEGÚN EL ROL
        // ===============================
        if ($rol == 1) {
            require __DIR__ . '/../views/admin/caso.php';
        } else {
            require __DIR__ . '/../views/comisionado/caso.php';
        }
    }

    // ===============================
    // CREAR CASO
    // ===============================
    public function store()
    {
        session_start();
        if (!isset($_SESSION['logged'])) header("Location: /project-cpr/public/login.php");

        $data = [
            'tipo_caso_id'       => $_POST['tipo_caso_id'] ?? null,
            'tipo_proceso_id'    => $_POST['tipo_proceso_id'] ?? null,
            'demandante_nombre'  => $_POST['demandante_nombre'] ?? null,
            'demandante_contacto' => $_POST['demandante_contacto'] ?? null,
            'asunto'             => $_POST['asunto'] ?? null,
            'detalles'           => $_POST['detalles'] ?? null,
            'estado'             => $_POST['estado'] ?? 'No atendido',
            'creado_por'         => $_SESSION['user']['id']
        ];

        Caso::create($data);
        header("Location: /project-cpr/casos");
    }

    // ===============================
    // ACTUALIZAR CASO
    // ===============================
    public function update()
    {
        session_start();
        if (!isset($_SESSION['logged'])) header("Location: /project-cpr/public/login.php");

        $id = $_POST['id'] ?? null;
        if (!$id) header("Location: /project-cpr/casos");

        $data = [
            'tipo_caso_id'       => $_POST['tipo_caso_id'] ?? null,
            'tipo_proceso_id'    => $_POST['tipo_proceso_id'] ?? null,
            'demandante_nombre'  => $_POST['demandante_nombre'] ?? null,
            'demandante_contacto' => $_POST['demandante_contacto'] ?? null,
            'asunto'             => $_POST['asunto'] ?? null,
            'detalles'           => $_POST['detalles'] ?? null,
            'estado'             => $_POST['estado'] ?? null
        ];

        Caso::update($id, $data);
        header("Location: /project-cpr/casos");
    }

    // ===============================
    // ELIMINAR CASO
    // ===============================
    public function delete()
    {
        session_start();
        if (!isset($_SESSION['logged'])) header("Location: /project-cpr/public/login.php");

        $id = $_POST['id'] ?? null;
        if ($id) Caso::delete($id);

        header("Location: /project-cpr/casos");
    }

// ===============================
// FILTRADO de casos POR BOTONES // VISTA Gestionar.php
// ===============================
public function gestionarFiltrado()
{
    $activePage = 'gestionar';
    $comisionado_id = $_SESSION['user']['id'];

    // Traemos todos los casos del comisionado logueado
    $casos_todos_comisionado = Caso::getByComisionado($comisionado_id);

    // Definimos los tiempos máximos por tipo de caso
    $tiempos_maximos = [
        'Denuncia'            => 30,
        'Solicitud'           => 15,
        'Derecho de petición' => 15,
        'Tutela'              => 10
    ];

    // Calculamos días restantes y casos urgentes
    $casos_urgentes = [];
    foreach ($casos_todos_comisionado as &$caso) {
        $tipo = $caso['tipo_caso_nombre'];
        $fecha_creacion = new DateTime($caso['fecha_creacion']);
        $hoy = new DateTime();

        $max_dias = $tiempos_maximos[$tipo] ?? 15; // default 15 días si no está en el array
        $interval = $fecha_creacion->diff($hoy);
        $dias_transcurridos = (int)$interval->format('%r%a'); // días desde creación
        $dias_restantes = $max_dias - $dias_transcurridos;

        // Guardamos días restantes en cada caso
        $caso['dias_restantes'] = $dias_restantes;

        // Solo consideramos NO atendidos/pendientes para urgentes
        if ($dias_restantes < 0 && $caso['estado'] !== 'Atendido') {
            $casos_urgentes[] = $caso;
        }
    }
    unset($caso); // romper referencia

    // Contar para los botones
    $casos_no_atendidos = array_filter($casos_todos_comisionado, fn($c) => $c['estado'] === 'No atendido');
    $casos_pendiente    = array_filter($casos_todos_comisionado, fn($c) => $c['estado'] === 'Pendiente');
    $casos_resueltos    = array_filter($casos_todos_comisionado, fn($c) => $c['estado'] === 'Atendido');
    $casos_todos        = $casos_todos_comisionado;

    // Filtramos según el botón seleccionado para mostrar en la tabla
    $filtro = $_GET['filtro'] ?? 'todos';
    switch ($filtro) {
        case 'no_atendido':
            $casos = $casos_no_atendidos;
            break;
        case 'pendiente':
            $casos = $casos_pendiente;
            break;
        case 'resueltos':
            $casos = $casos_resueltos;
            break;
        case 'urgentes':
            $casos = $casos_urgentes;
            break;
        case 'todos':
        default:
            $casos = $casos_todos;
            break;
    }

    require __DIR__ . '/../views/comisionado/gestionar.php';
}

}
