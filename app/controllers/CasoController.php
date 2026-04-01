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
        $tiposProceso = Caso::getTiposProcesoActivos();
        // Si el proceso actual está inactivo, lo agregamos para mostrarlo
        $procesoActualId = $caso['tipo_proceso_id'] ?? null;
        if ($procesoActualId) {
            $enLista = false;
            foreach ($tiposProceso as $p) {
                if ((int)$p['id'] === (int)$procesoActualId) {
                    $enLista = true;
                    break;
                }
            }
            if (!$enLista) {
                $procesoActual = Caso::getTipoProceso($procesoActualId);
                if ($procesoActual) {
                    $procesoActual['_inactivo'] = true;
                    $tiposProceso[] = $procesoActual;
                }
            }
        }
        $historial    = Caso::getHistorial($id);
        $mensajes     = Caso::getMensajes($id);
        $historialCampos = Caso::getHistorialCampos($id);

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
            'estado'               => 'Pendiente',
            'asignado_a'           => $_SESSION['user']['id'],
            'radicado_sena'        => $_POST['radicado_sena'] ?? null
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

        // Calculamos días restantes y casos próximos a vencer
        $casos_proximos = [];
        foreach ($casos_todos_comisionado as &$caso) {
            $fecha_creacion = new DateTime($caso['fecha_creacion']);
            $hoy = new DateTime();
            $hoy->setTime(0, 0, 0);
            $fecha_cierre = !empty($caso['fecha_cierre']) ? new DateTime($caso['fecha_cierre']) : null;
            if ($fecha_cierre) {
                $fecha_cierre->setTime(0, 0, 0);
            }

            $dias_restantes = null;
            if ($fecha_cierre) {
                $interval = $hoy->diff($fecha_cierre);
                $dias_restantes = (int)$interval->format('%r%a');
            }

            // Guardamos días restantes en cada caso
            $caso['dias_restantes'] = $dias_restantes;

            // Si se pasó el tiempo, pasa a No atendido
            if ($dias_restantes !== null && $dias_restantes < 0 && $caso['estado'] !== 'No atendido') {
                Caso::update($caso['id'], [
                    'tipo_caso_id' => $caso['tipo_caso_id'],
                    'tipo_proceso_id' => $caso['tipo_proceso_id'],
                    'asunto' => $caso['asunto'],
                    'detalles' => $caso['detalles'],
                    'estado' => 'No atendido'
                ]);
                $caso['estado'] = 'No atendido';
            }

            // Próximos a vencer: 0 a 5 días, no atendidos/pendientes
            if ($dias_restantes !== null && $dias_restantes >= 0 && $dias_restantes <= 5 && $caso['estado'] !== 'Atendido') {
                $casos_proximos[] = $caso;
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
            case 'proximos':
                $casos = $casos_proximos;
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
        $radicado_sena = trim($_POST['radicado_sena'] ?? '');
        $fecha_cierre_raw = trim($_POST['fecha_cierre'] ?? '');
        $fecha_cierre_actual = trim($_POST['fecha_cierre_actual'] ?? '');
        $fecha_cierre = $_POST['fecha_cierre'] ?? '';

        $usuario_creador_id = $_SESSION['user']['id'];

        // ===============================
        // 4. NORMALIZAR CAMPOS OPCIONALES
        // ===============================
        $asunto               = $asunto               !== '' ? $asunto               : null;
        $detalles             = $detalles             !== '' ? $detalles             : null;
        $radicado_sena         = $radicado_sena         !== '' ? $radicado_sena         : null;
        $fecha_cierre          = $fecha_cierre          !== '' ? $fecha_cierre          : null;

        // ===============================
        // 5. VALIDACIÓN MÍNIMA OBLIGATORIA
        // ===============================
        if (
            !$tipo_caso_id ||
            !$tipo_proceso_id ||
            !$fecha_cierre
        ) {
            header("Location: /project-cpr/public/gestionar.php?error=campos");
            exit;
        }

        // Validar que la fecha de cierre no sea anterior a hoy
        $hoy = new DateTime();
        $fc = DateTime::createFromFormat('Y-m-d', $fecha_cierre);
        if (!$fc || $fc < $hoy->setTime(0, 0, 0)) {
            // Guardar datos para repoblar el modal
            $_SESSION['form_gestionar'] = [
                'radicado_sena' => $radicado_sena,
                'tipo_caso_id' => $tipo_caso_id,
                'tipo_proceso_id' => $tipo_proceso_id,
                'asunto' => $asunto,
                'detalles' => $detalles,
                'fecha_cierre' => $fecha_cierre
            ];
            $_SESSION['error'] = "La fecha de cierre no puede ser anterior a hoy.";
            header("Location: /project-cpr/public/gestionar.php?error=fechacierre");
            exit;
        }

        // Guardar fecha de cierre con hora 23:59:59 para evitar confusión
        if ($fecha_cierre !== null) {
            $fecha_cierre = $fecha_cierre . ' 23:59:59';
        }

        // ===============================
        // 6. GUARDAR CASO (MODELO ÚNICO)
        // ===============================
        $resultado = Caso::create([
            'tipo_caso_id'         => $tipo_caso_id,
            'tipo_proceso_id'      => $tipo_proceso_id,
            'asunto'               => $asunto,               // NULL permitido
            'detalles'             => $detalles,             // NULL permitido
            'estado'               => 'Pendiente',
            'asignado_a'           => $usuario_creador_id,
            'radicado_sena'        => $radicado_sena,
            'fecha_cierre'         => $fecha_cierre
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
        $tipo_caso_id_nuevo = $_POST['tipo_caso_id'] ?? $caso['tipo_caso_id'];

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

    public function updateCampos($id)
    {
        // Actualiza asunto, detalles y radicado (con historial).
        $caso = Caso::find($id);
        if (!$caso) {
            die("Caso no encontrado.");
        }

        $usuario_id = $_SESSION['user']['id'];

        $asunto = trim($_POST['asunto'] ?? '');
        $detalles = trim($_POST['detalles'] ?? '');
        $radicado_sena = trim($_POST['radicado_sena'] ?? '');

        $asunto = $asunto !== '' ? $asunto : null;
        $detalles = $detalles !== '' ? $detalles : null;
        $radicado_sena = $radicado_sena !== '' ? $radicado_sena : null;
        $fecha_cierre = $caso['fecha_cierre'] ?? null;

        // Si el usuario envía fecha, validar y aplicar 23:59:59
        if ($fecha_cierre_raw !== '' && $fecha_cierre_raw !== $fecha_cierre_actual) {
            $hoy = new DateTime();
            $fc = DateTime::createFromFormat('Y-m-d', $fecha_cierre_raw);
            if (!$fc || $fc < $hoy->setTime(0, 0, 0)) {
                $_SESSION['error'] = "La fecha de cierre no puede ser anterior a hoy.";
                header("Location: /project-cpr/public/caso.php?id=$id&error=fechacierre");
                exit;
            }
            $fecha_cierre = $fecha_cierre_raw . ' 23:59:59';
        }

        $cambios = [];

        if ($caso['asunto'] !== $asunto) {
            $cambios[] = [
                'campo' => 'asunto',
                'anterior' => $caso['asunto'],
                'nuevo' => $asunto
            ];
        }

        if ($caso['detalles'] !== $detalles) {
            $cambios[] = [
                'campo' => 'detalles',
                'anterior' => $caso['detalles'],
                'nuevo' => $detalles
            ];
        }

        if (($caso['radicado_sena'] ?? null) !== $radicado_sena) {
            $cambios[] = [
                'campo' => 'radicado_sena',
                'anterior' => $caso['radicado_sena'] ?? null,
                'nuevo' => $radicado_sena
            ];
        }

        if (($caso['fecha_cierre'] ?? null) !== $fecha_cierre) {
            $cambios[] = [
                'campo' => 'fecha_cierre',
                'anterior' => $caso['fecha_cierre'] ?? null,
                'nuevo' => $fecha_cierre
            ];
        }

        if (!empty($cambios)) {
            Caso::updateCampos($id, [
                'asunto' => $asunto,
                'detalles' => $detalles,
                'radicado_sena' => $radicado_sena,
                'fecha_cierre' => $fecha_cierre
            ]);

            foreach ($cambios as $c) {
                Caso::guardarHistorialCampo([
                    'caso_id' => $id,
                    'usuario_id' => $usuario_id,
                    'campo' => $c['campo'],
                    'valor_anterior' => $c['anterior'],
                    'valor_nuevo' => $c['nuevo']
                ]);
            }
        }

        header("Location: /project-cpr/public/caso.php?id=$id");
        exit;
    }
}
