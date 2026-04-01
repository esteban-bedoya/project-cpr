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

// ===============================
// CREAR CASO DESDE GESTIONAR
// ===============================
public function storeGestionar()
{
    // La sesión ya está iniciada desde el front controller
    if (!isset($_SESSION['logged'])) {
        header("Location: /project-cpr/public/login.php");
        exit;
    }

    $usuario_creador_id = $_SESSION['user']['id'];

    // Validar campos mínimos
    $tipo_proceso_id = $_POST['tipo_proceso_id'] ?? null;
    $demandante_nombre = trim($_POST['demandante_nombre'] ?? '');
    $demandante_contacto = trim($_POST['demandante_contacto'] ?? '');
    $asunto = trim($_POST['asunto'] ?? '');
    $detalles = trim($_POST['detalles'] ?? '');
    $asignado_a = $_POST['asignado_a'] ?? null;

    if (!$tipo_proceso_id || !$demandante_nombre || !$asignado_a) {
        // Podrías redirigir con error o manejarlo como quieras
        header("Location: /project-cpr/public/gestionar.php?error=1");
        exit;
    }

    // Obtener el tipo de caso automáticamente según el tipo de proceso
    $db = new PDO("mysql:host=localhost;dbname=project-cpr;charset=utf8", "root", "");
    $stmt = $db->prepare("SELECT tipo_caso_id FROM tipos_proceso WHERE id = ?");
    $stmt->execute([$tipo_proceso_id]);
    $tipo_caso_id = $stmt->fetchColumn();

    // Crear caso en la tabla 'casos'
    $sql = "INSERT INTO casos 
            (tipo_caso_id, tipo_proceso_id, demandante_nombre, demandante_contacto, asunto, detalles, estado, creado_por, asignado_a)
            VALUES (:tipo_caso_id, :tipo_proceso_id, :demandante_nombre, :demandante_contacto, :asunto, :detalles, 'No atendido', :creado_por, :asignado_a)";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':tipo_caso_id'       => $tipo_caso_id,
        ':tipo_proceso_id'    => $tipo_proceso_id,
        ':demandante_nombre'  => $demandante_nombre,
        ':demandante_contacto'=> $demandante_contacto,
        ':asunto'             => $asunto,
        ':detalles'           => $detalles,
        ':creado_por'         => $usuario_creador_id,
        ':asignado_a'         => $asignado_a
    ]);

    // Obtener el ID recién creado
    $caso_id = $db->lastInsertId();

    // Guardar histórico en casos_asignaciones
    $sqlHist = "INSERT INTO casos_asignaciones (caso_id, comisionado_id, asignado_por) VALUES (:caso_id, :comisionado_id, :asignado_por)";
    $stmtHist = $db->prepare($sqlHist);
    $stmtHist->execute([
        ':caso_id'         => $caso_id,
        ':comisionado_id'  => $asignado_a,
        ':asignado_por'    => $usuario_creador_id
    ]);

    // Redirigir de vuelta a gestionar
    header("Location: /project-cpr/public/gestionar.php?success=1");
    exit;
}

public function updateDetalle($id)
{
    // La sesión ya está iniciada en el front controller
    if (!isset($_SESSION['logged'])) {
        header("Location: /project-cpr/public/login.php");
        exit;
    }

    $caso = Caso::find($id);
    if (!$caso) {
        die("Caso no encontrado.");
    }

    $usuario_id = $_SESSION['user']['id'];

    $comisionado_id = $_POST['comisionado_id'] ?? $caso['asignado_a'];
    $estado = $_POST['estado'] ?? $caso['estado'];
    $tipo_proceso_id = $_POST['tipo_proceso_id'] ?? $caso['tipo_proceso_id'];

    // Obtenemos el tipo de caso según el tipo de proceso
    $tipo_proceso = Caso::getTiposProceso();
    $tipo_caso_id_nuevo = null;
    foreach ($tipo_proceso as $tp) {
        if ($tp['id'] == $tipo_proceso_id) {
            $tipo_caso_id_nuevo = $tp['tipo_caso_id'];
            break;
        }
    }
    $tipo_caso_id_actual = $caso['tipo_caso_id'];

    // ============================================
    // Guardar historial en una sola tabla
    // ============================================
    $historialCambios = [];

    if ($caso['asignado_a'] != $comisionado_id) {
        $nuevoComisionado = Caso::getUsuario($comisionado_id);
        $antiguoComisionado = Caso::getUsuario($caso['asignado_a']);
        $historialCambios[] = [
            'caso_id' => $id,
            'usuario_id' => $usuario_id,
            'descripcion' => "Asignó a {$nuevoComisionado['username']} como comisionado (antes: {$antiguoComisionado['username']})"
        ];
    }

    if ($caso['estado'] != $estado) {
        $historialCambios[] = [
            'caso_id' => $id,
            'usuario_id' => $usuario_id,
            'descripcion' => "Cambio de estado de {$caso['estado']} a {$estado}"
        ];
    }

    if ($caso['tipo_proceso_id'] != $tipo_proceso_id) {
        $tipoProcesoAnt = Caso::getTipoProceso($caso['tipo_proceso_id']);
        $tipoProcesoNuevo = Caso::getTipoProceso($tipo_proceso_id);
        $historialCambios[] = [
            'caso_id' => $id,
            'usuario_id' => $usuario_id,
            'descripcion' => "Cambio de tipo de proceso de {$tipoProcesoAnt['nombre']} a {$tipoProcesoNuevo['nombre']}"
        ];
    }

    if ($tipo_caso_id_actual != $tipo_caso_id_nuevo) {
        $tipoCasoAnt = Caso::getTipoCaso($tipo_caso_id_actual);
        $tipoCasoNuevo = Caso::getTipoCaso($tipo_caso_id_nuevo);
        $historialCambios[] = [
            'caso_id' => $id,
            'usuario_id' => $usuario_id,
            'descripcion' => "Cambio de tipo de caso de {$tipoCasoAnt['nombre']} a {$tipoCasoNuevo['nombre']}"
        ];
    }

    // Guardar historial
    foreach ($historialCambios as $h) {
        Caso::guardarHistorial($h);
    }

    // Actualizar datos en la tabla casos
    Caso::update($id, [
        'asignado_a' => $comisionado_id,
        'estado' => $estado,
        'tipo_proceso_id' => $tipo_proceso_id,
        'tipo_caso_id' => $tipo_caso_id_nuevo
    ]);

    header("Location: /project-cpr/casos/$id");
    exit;
}




}
