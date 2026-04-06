<?php
// Controlador de casos: listado, detalle, creación, actualización y mensajes.
require_once __DIR__ . '/../models/Caso.php';
require_once __DIR__ . '/../models/User.php';

class CasoController
{
    // Texto comun para cierres automaticos por vencimiento.
    private function descripcionNoAtendidoAutomatico($fechaCierre)
    {
        return 'Cambio de estado automático del sistema de Pendiente a No atendido porque no recibió atención dentro del plazo establecido';
    }

    // Solo el comisionado asignado puede editar.
    private function puedeEditarCaso($caso)
    {
        $usuario = $_SESSION['user'] ?? [];
        $esComisionado = (int)($usuario['rol'] ?? 0) === 2;
        $esCreadorDelCaso = (int)($usuario['id'] ?? 0) === (int)($caso['asignado_a'] ?? 0);

        return $esComisionado && $esCreadorDelCaso;
    }

    private function getSystemUserId()
    {
        // Usa el usuario "Sistema" para cambios automáticos.
        $system = User::findByUsername('Sistema');
        if ($system && isset($system['id'])) {
            return (int)$system['id'];
        }
        // Fallback defensivo: si no existe, usa el usuario en sesión.
        return (int)($_SESSION['user']['id'] ?? 0);
    }
    // ===============================
    // MOSTRAR TODOS LOS CASOS
    // ===============================
    public function index()
    {
        // Se valida el rol (admin o comisionado) antes de mostrar el listado.
        $rol = $_SESSION['user']['rol'];
        if (!in_array($rol, [1, 2])) header("Location: /project-cpr/public/login.php");

        // Datos base para filtros y tabla.
        $casos = Caso::all();
        $tiposCaso = Caso::getTiposCaso();
        $tiposProceso = Caso::getTiposProceso();
        $comisionados = User::getComisionadosAll();

        // ===============================
        // FILTROS (via GET)
        // ===============================
        $filtro_estado = $_GET['estado'] ?? 'todos';
        $filtro_tipo_caso = $_GET['tipo_caso'] ?? 'todos';
        $filtro_tipo_proceso = $_GET['tipo_proceso'] ?? 'todos';
        $filtro_comisionado = $_GET['comisionado'] ?? 'todos';
        $fecha_inicio = trim($_GET['fecha_inicio'] ?? '');
        $fecha_fin = trim($_GET['fecha_fin'] ?? '');
        $filtros_aplicados = isset($_GET['aplicar']) && $_GET['aplicar'] === '1';

        $fecha_inicio_dt = $fecha_inicio !== '' ? DateTime::createFromFormat('Y-m-d', $fecha_inicio) : null;
        $fecha_fin_dt = $fecha_fin !== '' ? DateTime::createFromFormat('Y-m-d', $fecha_fin) : null;

        $casos_filtrados = [];
        $hoy = new DateTime();

        if ($filtros_aplicados) {
            // Aqui tambien se actualizan casos vencidos.
            foreach ($casos as $caso) {
                // Auto actualizar a No atendido si vencido y pendiente
                if (!empty($caso['fecha_cierre']) && $caso['estado'] === 'Pendiente') {
                    try {
                        $fc = new DateTime($caso['fecha_cierre']);
                        if ($fc < $hoy) {
                            Caso::update($caso['id'], [
                                'tipo_caso_id' => $caso['tipo_caso_id'],
                                'tipo_proceso_id' => $caso['tipo_proceso_id'],
                                'asunto' => $caso['asunto'],
                                'detalles' => $caso['detalles'],
                                'estado' => 'No atendido'
                            ]);
                            Caso::guardarHistorial([
                                'caso_id' => $caso['id'],
                                'usuario_id' => $this->getSystemUserId(),
                                'descripcion' => $this->descripcionNoAtendidoAutomatico($caso['fecha_cierre'])
                            ]);
                            $caso['estado'] = 'No atendido';
                        }
                    } catch (Exception $e) {
                        // Sin accion si falla la fecha
                    }
                }

                // Rango de fechas (fecha_creacion)
                if ($fecha_inicio_dt || $fecha_fin_dt) {
                    $fc = !empty($caso['fecha_creacion']) ? new DateTime($caso['fecha_creacion']) : null;
                    if ($fc) {
                        if ($fecha_inicio_dt && $fc < $fecha_inicio_dt) continue;
                        if ($fecha_fin_dt && $fc > (clone $fecha_fin_dt)->setTime(23, 59, 59)) continue;
                    }
                }

                // Tipo de caso
                if ($filtro_tipo_caso !== 'todos' && (string)$caso['tipo_caso_id'] !== (string)$filtro_tipo_caso) {
                    continue;
                }

                // Tipo de proceso
                if ($filtro_tipo_proceso !== 'todos' && (string)$caso['tipo_proceso_id'] !== (string)$filtro_tipo_proceso) {
                    continue;
                }

                // Comisionado asignado
                if ($filtro_comisionado !== 'todos' && (string)$caso['asignado_a'] !== (string)$filtro_comisionado) {
                    continue;
                }

                // Estado del caso
                if ($filtro_estado !== 'todos') {
                    if ($caso['estado'] !== $filtro_estado) {
                        continue;
                    }
                }

                $casos_filtrados[] = $caso;
            }
        }

        // La tabla solo se llena si el usuario aplica filtros.
        $casos = $casos_filtrados;

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

        // Actualiza el estado si la fecha de cierre ya vencio.
        if (!empty($caso['fecha_cierre'])) {
            $hoy = new DateTime();
            $fecha_cierre = new DateTime($caso['fecha_cierre']);
            if ($fecha_cierre < $hoy && $caso['estado'] === 'Pendiente') {
                Caso::update($caso['id'], [
                    'tipo_caso_id' => $caso['tipo_caso_id'],
                    'tipo_proceso_id' => $caso['tipo_proceso_id'],
                    'asunto' => $caso['asunto'],
                    'detalles' => $caso['detalles'],
                    'estado' => 'No atendido'
                ]);
                Caso::guardarHistorial([
                    'caso_id' => $caso['id'],
                    'usuario_id' => $this->getSystemUserId(),
                    'descripcion' => $this->descripcionNoAtendidoAutomatico($caso['fecha_cierre'])
                ]);
                $caso['estado'] = 'No atendido';
            }
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
        $usuarioPuedeEditar = $this->puedeEditarCaso($caso);

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

        foreach ($casos_todos_comisionado as &$caso) {
            $hoy = new DateTime();
            $fecha_cierre = !empty($caso['fecha_cierre']) ? new DateTime($caso['fecha_cierre']) : null;

            // Si se pasó el tiempo, pasa a No atendido
            if ($fecha_cierre !== null && $fecha_cierre < $hoy && $caso['estado'] === 'Pendiente') {
                Caso::update($caso['id'], [
                    'tipo_caso_id' => $caso['tipo_caso_id'],
                    'tipo_proceso_id' => $caso['tipo_proceso_id'],
                    'asunto' => $caso['asunto'],
                    'detalles' => $caso['detalles'],
                    'estado' => 'No atendido'
                ]);
                Caso::guardarHistorial([
                    'caso_id' => $caso['id'],
                    'usuario_id' => $this->getSystemUserId(),
                    'descripcion' => $this->descripcionNoAtendidoAutomatico($caso['fecha_cierre'])
                ]);
                $caso['estado'] = 'No atendido';
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
            header("Location: /project-cpr/public/casos.php");
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
            header("Location: /project-cpr/public/casos.php?error=campos");
            exit;
        }

        // Validar que la fecha de cierre no sea anterior a hoy.
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
            header("Location: /project-cpr/public/casos.php?error=fechacierre");
            exit;
        }

        // Guardar la fecha con la hora final del día evita que el caso se marque
        // como vencido desde la medianoche del mismo día ingresado por el usuario.
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
            header("Location: /project-cpr/public/casos.php?success=1");
        } else {
            header("Location: /project-cpr/public/casos.php?error=db");
        }

        exit;
    }

    public function updateDetalle($id)
    {
        // Esta acción maneja la edición desde la ficha del caso, donde además
        // de guardar los cambios se construye el historial legible para auditoría.
        $caso = Caso::find($id);
        if (!$caso) {
            die("Caso no encontrado.");
        }

        if (!$this->puedeEditarCaso($caso)) {
            header("Location: /project-cpr/public/caso.php?id=$id");
            exit;
        }

        $usuario_id = $_SESSION['user']['id'];
        $estado = $_POST['estado'] ?? $caso['estado'];
        $tipoProcesoId = $_POST['tipo_proceso_id'] ?? $caso['tipo_proceso_id'];
        $tipoCasoId = $_POST['tipo_caso_id'] ?? $caso['tipo_caso_id'];
        $fechaCierre = $caso['fecha_cierre'] ?? null;
        $fechaCierreAnterior = $caso['fecha_cierre'] ?? null;
        $cambiosHistorial = [];

        // Si el caso ya venció, no se permite dejarlo abierto sin antes
        // corregir la fecha de cierre. Eso obliga a reabrirlo explícitamente.
        if ($estado !== 'Atendido' && !empty($caso['fecha_cierre'])) {
            $hoy = new DateTime();
            $fechaCierreActual = new DateTime($caso['fecha_cierre']);
            if ($fechaCierreActual < $hoy) {
                $_SESSION['error'] = "La fecha de cierre esta vencida. Actualizala para reabrir el caso.";
                header("Location: /project-cpr/public/caso.php?id=$id&error=fechacierre");
                exit;
            }
        }

        // Al marcar como atendido, la fecha de cierre pasa a ser la fecha real
        // de resolución. Así el historial refleja cuándo terminó de verdad.
        if ($estado === 'Atendido' && $caso['estado'] !== 'Atendido') {
            $fechaCierre = (new DateTime())->format('Y-m-d H:i:s');
        }

        // Cada cambio relevante se convierte en un texto de historial.
        // Luego todos se insertan antes del update final para dejar trazabilidad.
        if ($caso['estado'] != $estado) {
            $descripcionEstado = "Cambio de estado de {$caso['estado']} a {$estado}";

            if ($caso['estado'] === 'Pendiente' && $estado === 'Atendido') {
                $descripcionEstado .= " con actualización de fecha de cierre, de \"{$fechaCierreAnterior}\" a \"{$fechaCierre}\"";
            }

            $cambiosHistorial[] = [
                'caso_id' => $id,
                'usuario_id' => $usuario_id,
                'descripcion' => $descripcionEstado
            ];
        }

        if ($caso['tipo_proceso_id'] != $tipoProcesoId) {
            $tipoProcesoAnt = Caso::getTipoProceso($caso['tipo_proceso_id']);
            $tipoProcesoNuevo = Caso::getTipoProceso($tipoProcesoId);
            $cambiosHistorial[] = [
                'caso_id' => $id,
                'usuario_id' => $usuario_id,
                'descripcion' => "Cambio de tipo de proceso de {$tipoProcesoAnt['nombre']} a {$tipoProcesoNuevo['nombre']}"
            ];
        }

        if ($caso['tipo_caso_id'] != $tipoCasoId) {
            $tipoCasoAnt = Caso::getTipoCaso($caso['tipo_caso_id']);
            $tipoCasoNuevo = Caso::getTipoCaso($tipoCasoId);
            $cambiosHistorial[] = [
                'caso_id' => $id,
                'usuario_id' => $usuario_id,
                'descripcion' => "Cambio de tipo de caso de {$tipoCasoAnt['nombre']} a {$tipoCasoNuevo['nombre']}"
            ];
        }

        foreach ($cambiosHistorial as $cambio) {
            Caso::guardarHistorial($cambio);
        }

        Caso::updateDetalle($id, [
            'estado' => $estado,
            'tipo_proceso_id' => $tipoProcesoId,
            'tipo_caso_id' => $tipoCasoId,
            'fecha_cierre' => $fechaCierre
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
        $caso = Caso::find($caso_id);
        if (!$caso) {
            die("Caso no encontrado.");
        }

        if (!$this->puedeEditarCaso($caso)) {
            header("Location: /project-cpr/public/caso.php?id=$caso_id");
            exit;
        }

        if (($caso['estado'] ?? '') === 'Pendiente' && !empty($caso['fecha_cierre'])) {
            $hoy = new DateTime();
            $fechaCierreActual = new DateTime($caso['fecha_cierre']);
            if ($fechaCierreActual < $hoy) {
                header("Location: /project-cpr/public/caso.php?id=$caso_id");
                exit;
            }
        }

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
        $caso = Caso::find($id);
        if (!$caso) {
            die("Caso no encontrado.");
        }

        if (!$this->puedeEditarCaso($caso)) {
            header("Location: /project-cpr/public/caso.php?id=$id");
            exit;
        }

        $usuario_id = $_SESSION['user']['id'];
        $radicado_sena = trim($_POST['radicado_sena'] ?? '');
        $fecha_cierre_raw = trim($_POST['fecha_cierre'] ?? '');
        $radicado_sena = $radicado_sena !== '' ? $radicado_sena : null;
        $fecha_cierre = $caso['fecha_cierre'] ?? null;
        $fecha_cierre_anterior = $caso['fecha_cierre'] ?? null;
        $fecha_cierre_actual = $fecha_cierre ? date('Y-m-d', strtotime($fecha_cierre)) : '';
        $fecha_nueva_ts = $fecha_cierre_raw !== '' ? strtotime($fecha_cierre_raw) : null;
        $fecha_actual_ts = $fecha_cierre_actual !== '' ? strtotime($fecha_cierre_actual) : null;
        $fecha_cierre_cambio_real = false;

        if ($fecha_cierre_raw !== '') {
            $fecha_cierre_cambio_real = !($fecha_nueva_ts !== null && $fecha_actual_ts !== null && $fecha_nueva_ts === $fecha_actual_ts);
            if ($fecha_cierre_cambio_real) {
                $hoy = new DateTime('today');
                $fc = DateTime::createFromFormat('Y-m-d', $fecha_cierre_raw);
                if (!$fc || $fc <= $hoy) {
                    $_SESSION['error'] = "La fecha de cierre debe ser posterior a hoy.";
                    header("Location: /project-cpr/public/caso.php?id=$id&error=fechacierre");
                    exit;
                }
                $fecha_cierre = $fecha_cierre_raw . ' 23:59:59';
            }
        } elseif ($fecha_cierre_raw === '' && $fecha_cierre_actual === '') {
            $fecha_cierre = null;
        }

        $cambios = [];

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

        $reabrePorFecha = !empty($fecha_cierre)
            && $fecha_cierre_cambio_real
            && $caso['estado'] !== 'Pendiente';

        if (!empty($cambios)) {
            Caso::updateCampos($id, [
                'radicado_sena' => $radicado_sena,
                'fecha_cierre' => $fecha_cierre
            ]);

            foreach ($cambios as $c) {
                if ($reabrePorFecha && $c['campo'] === 'fecha_cierre') {
                    continue;
                }

                Caso::guardarHistorialCampo([
                    'caso_id' => $id,
                    'usuario_id' => $usuario_id,
                    'campo' => $c['campo'],
                    'valor_anterior' => $c['anterior'],
                    'valor_nuevo' => $c['nuevo']
                ]);
            }
        }

        if (!empty($fecha_cierre)) {
            $hoy = new DateTime();
            $fc = new DateTime($fecha_cierre);
            if ($fc >= $hoy && $caso['estado'] !== 'Pendiente') {
                Caso::update($id, [
                    'tipo_caso_id' => $caso['tipo_caso_id'],
                    'tipo_proceso_id' => $caso['tipo_proceso_id'],
                    'asunto' => $caso['asunto'],
                    'detalles' => $caso['detalles'],
                    'estado' => 'Pendiente'
                ]);
                Caso::guardarHistorial([
                    'caso_id' => $id,
                    'usuario_id' => $usuario_id,
                    'descripcion' => "Cambio de estado de {$caso['estado']} a Pendiente con actualización de fecha de cierre, de \"{$fecha_cierre_anterior}\" a \"{$fecha_cierre}\""
                ]);
            }
        }

        header("Location: /project-cpr/public/caso.php?id=$id");
        exit;
    }
}
