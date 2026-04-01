<?php
// Controlador de casos: listado, detalle, creación, actualización y mensajes.
require_once __DIR__ . '/../models/Caso.php';

class CasoController
{
    // ===============================
    // MOSTRAR TODOS LOS CASOS
    // ===============================
    public function index()
    {
        // Se valida el rol (admin o comisionado) antes de mostrar el listado.
        $rol = $_SESSION['user']['rol'];
        if (!in_array($rol, [1, 2])) header("Location: /project-cpr/public/login.php");

        // Obtiene todos los casos desde el modelo.
        $casos = Caso::all();

        // Selecciona la vista segun el rol.
        $view = $rol == 1 ? 'admin' : 'comisionado';
        require __DIR__ . "/../views/{$view}/casos.php";
    }

    // ===============================
    // MOSTRAR UN CASO
    // ===============================
    public function show($id)
    {
        // Acceso restringido a roles autorizados.
        $rol = $_SESSION['user']['rol'];
        if (!in_array($rol, [1, 2])) {
            header("Location: /project-cpr/public/login.php");
            exit;
        }

        // Busca el caso por id.
        $caso = Caso::find($id);
        if (!$caso) {
            header("Location: /project-cpr/casos");
            exit;
        }

        // =====================================
        // DATOS PARA LA VISTA
        // =====================================
        // Catalogos y datos de soporte para renderizar la vista.
        $tiposCaso    = Caso::getTiposCaso();
        $tiposProceso = Caso::getTiposProceso();
        $historial    = Caso::getHistorial($id);
        $mensajes     = Caso::getMensajes($id);

        // =====================================
        // CARGAR VISTA SEGÚN ROL
        // =====================================
        $view = $rol == 1 ? 'admin' : 'comisionado';
        require __DIR__ . "/../views/{$view}/caso.php";
    }



    // ===============================
    // CREAR CASO
    // ===============================
    public function store()
    {
        // Arma la data basica del caso desde el formulario.
        $data = [
            'tipo_caso_id'         => $_POST['tipo_caso_id'] ?? null,
            'tipo_proceso_id'      => $_POST['tipo_proceso_id'] ?? null,
            'asunto'               => $_POST['asunto'] ?? null,
            'detalles'             => $_POST['detalles'] ?? null,
            'estado'               => 'No atendido',
            'asignado_a'           => $_SESSION['user']['id']
        ];

        // Inserta el caso en BD y redirige al listado.
        Caso::create($data);
        header("Location: /project-cpr/casos");
    }


    // ===============================
    // ACTUALIZAR CASO
    // ===============================
    public function update()
    {
        // Se obtiene el id del caso a actualizar.
        $id = $_POST['id'] ?? null;
        if (!$id) header("Location: /project-cpr/casos");

        // Datos actualizables del caso.
        $data = [
            'tipo_caso_id'       => $_POST['tipo_caso_id'] ?? null,
            'tipo_proceso_id'    => $_POST['tipo_proceso_id'] ?? null,
            'asunto'             => $_POST['asunto'] ?? null,
            'detalles'           => $_POST['detalles'] ?? null,
            'estado'             => $_POST['estado'] ?? null
        ];

        // Actualiza en BD y vuelve al listado.
        Caso::update($id, $data);
        header("Location: /project-cpr/casos");
    }

    // ===============================
    // ELIMINAR CASO
    // ===============================
    public function delete()
    {
        // Elimina el caso si existe id valido.
        $id = $_POST['id'] ?? null;
        if ($id) Caso::delete($id);

        header("Location: /project-cpr/casos");
    }

    // ===============================
    // FILTRADO de casos POR BOTONES // VISTA Gestionar.php
    // ===============================
    public function gestionarFiltrado()
    {
        // Pantalla "Gestionar": se filtra segun botones de estado/urgencia.
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

        // Carga la vista con el listado ya filtrado.
        require __DIR__ . '/../views/comisionado/gestionar.php';
    }

    // ===============================
    // CREAR/GUARDAR CASO DESDE GESTIONAR
    // ===============================
    public function storeGestionar()
    {
        // ===============================
        // 1. SEGURIDAD
        // ===============================
        if (!isset($_SESSION['logged'])) {
            header("Location: /project-cpr/public/login.php");
            exit;
        }

        // ===============================
        // 2. VALIDAR MÉTODO
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /project-cpr/public/gestionar.php");
            exit;
        }

        // ===============================
        // 3. CAPTURA Y LIMPIEZA DE DATOS
        // ===============================
        $tipo_caso_id    = (int) ($_POST['tipo_caso_id'] ?? 0);
        $tipo_proceso_id = (int) ($_POST['tipo_proceso_id'] ?? 0);

        $asunto   = trim($_POST['asunto'] ?? '');
        $detalles = trim($_POST['detalles'] ?? '');

        $usuario_creador_id = $_SESSION['user']['id'];

        // ===============================
        // 4. NORMALIZAR CAMPOS OPCIONALES
        // ===============================
        $asunto               = $asunto               !== '' ? $asunto               : null;
        $detalles             = $detalles             !== '' ? $detalles             : null;

        // ===============================
        // 5. VALIDACIÓN MÍNIMA OBLIGATORIA
        // ===============================
        if (
            !$tipo_caso_id ||
            !$tipo_proceso_id
        ) {
            header("Location: /project-cpr/public/gestionar.php?error=campos");
            exit;
        }

        // ===============================
        // 6. GUARDAR CASO (MODELO ÚNICO)
        // ===============================
        $resultado = Caso::create([
            'tipo_caso_id'         => $tipo_caso_id,
            'tipo_proceso_id'      => $tipo_proceso_id,
            'asunto'               => $asunto,               // NULL permitido
            'detalles'             => $detalles,             // NULL permitido
            'estado'               => 'No atendido',
            'asignado_a'           => $usuario_creador_id
        ]);

        // ===============================
        // 7. RESULTADO
        // ===============================
        if ($resultado) {
            header("Location: /project-cpr/public/gestionar.php?success=1");
        } else {
            header("Location: /project-cpr/public/gestionar.php?error=db");
        }

        exit;
    }

    public function updateDetalle($id)
    {
        // Carga el caso actual para comparar cambios.
        $caso = Caso::find($id);
        if (!$caso) {
            die("Caso no encontrado.");
        }

        $usuario_id = $_SESSION['user']['id'];

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
        Caso::updateDetalle($id, [
            'estado' => $estado,
            'tipo_proceso_id' => $tipo_proceso_id,
            'tipo_caso_id' => $tipo_caso_id_nuevo
        ]);

        header("Location: /project-cpr/public/caso.php?id=$id");
        exit;
    }

    public function storeMensaje($caso_id)
    {
        // ===============================
        // SEGURIDAD
        // ===============================
        if (!isset($_SESSION['logged'])) {
            header("Location: /project-cpr/public/login.php");
            exit;
        }

        // ===============================
        // DATOS BASE
        // ===============================
        // Mensaje y archivo son opcionales, pero no pueden venir ambos vacios.
        $mensaje = trim($_POST['mensaje'] ?? '');
        $hayArchivo = isset($_FILES['archivo']) && $_FILES['archivo']['error'] === 0;
        $archivoNombre = null;

        // Se permite enviar:
        // - solo mensaje
        // - solo archivo
        // - mensaje + archivo

        // ===============================
        // VALIDACIÓN PRINCIPAL
        // ===============================
        if ($mensaje === '' && !$hayArchivo) {
            header("Location: /project-cpr/public/caso.php?id=$caso_id&error=vacio");
            exit;
        }

        // ===============================
        // VALIDACIÓN Y SUBIDA DE ARCHIVO
        // ===============================
        if ($hayArchivo) {

            $permitidos = ['pdf', 'jpg', 'jpeg', 'png'];
            $maxSize = 5 * 1024 * 1024; // 5 MB

            $extension = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
            $tamano = $_FILES['archivo']['size'];

            if (!in_array($extension, $permitidos)) {
                header("Location: /project-cpr/public/caso.php?id=$caso_id&error=tipo");
                exit;
            }

            if ($tamano > $maxSize) {
                header("Location: /project-cpr/public/caso.php?id=$caso_id&error=tamano");
                exit;
            }

            $carpeta = realpath(__DIR__ . '/../../public') . '/uploads/casos/';

            if (!is_dir($carpeta)) {
                mkdir($carpeta, 0777, true);
            }

            $archivoNombre = 'caso_' . $caso_id . '_' . time() . '.' . $extension;

            if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $carpeta . $archivoNombre)) {
                header("Location: /project-cpr/public/caso.php?id=$caso_id&error=subida");
                exit;
            }
        }

        // ===============================
        // GUARDAR MENSAJE
        // ===============================
        // Inserta el mensaje y opcionalmente referencia al archivo subido.
        Caso::guardarMensaje([
            'caso_id'    => $caso_id,
            'usuario_id' => $_SESSION['user']['id'],
            'mensaje'    => $mensaje,
            'archivo'    => $archivoNombre
        ]);

        header("Location: /project-cpr/public/caso.php?id=$caso_id");
        exit;
    }
}
