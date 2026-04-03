<?php
// Front controller de reportes: carga vista segun rol.
session_start();

require_once __DIR__ . '/../app/models/Caso.php';
require_once __DIR__ . '/../app/models/User.php';

// ===============================
// SEGURIDAD
// ===============================
if (!isset($_SESSION['user'])) {
    header("Location: /project-cpr/public/login.php");
    exit;
}

// ===============================
// CARGAR VISTA SEGÚN ROL
// ===============================
$rol = $_SESSION['user']['rol'];
$usuarioId = (int)($_SESSION['user']['id'] ?? 0);

$fecha_inicio = trim($_GET['fecha_inicio'] ?? '');
$fecha_fin = trim($_GET['fecha_fin'] ?? '');
$error_reporte = '';

$fecha_inicio_dt = $fecha_inicio !== '' ? DateTime::createFromFormat('Y-m-d', $fecha_inicio) : null;
$fecha_fin_dt = $fecha_fin !== '' ? DateTime::createFromFormat('Y-m-d', $fecha_fin) : null;

if ($fecha_inicio !== '' && !$fecha_inicio_dt) {
    $error_reporte = 'La fecha inicial no es válida.';
}

if ($fecha_fin !== '' && !$fecha_fin_dt) {
    $error_reporte = 'La fecha final no es válida.';
}

if ($fecha_inicio_dt && $fecha_fin_dt && $fecha_inicio_dt > $fecha_fin_dt) {
    $error_reporte = 'La fecha inicial no puede ser mayor que la fecha final.';
}

$comisionados = User::getComisionadosAll();
$filtro_comisionado = $_GET['comisionado'] ?? 'todos';

if ($rol == 2) {
    $filtro_comisionado = (string)$usuarioId;
}

$casos = $rol == 1 ? Caso::all() : Caso::getByComisionado($usuarioId);
$casosFiltrados = [];
$totalesEstado = [
    'Atendido' => 0,
    'Pendiente' => 0,
    'No atendido' => 0
];

foreach ($casos as $caso) {
    if ($rol == 1 && $filtro_comisionado !== 'todos' && (string)($caso['asignado_a'] ?? '') !== (string)$filtro_comisionado) {
        continue;
    }

    $fechaCaso = !empty($caso['fecha_creacion']) ? new DateTime($caso['fecha_creacion']) : null;
    if ($fechaCaso) {
        if ($fecha_inicio_dt && $fechaCaso < $fecha_inicio_dt) {
            continue;
        }
        if ($fecha_fin_dt && $fechaCaso > (clone $fecha_fin_dt)->setTime(23, 59, 59)) {
            continue;
        }
    }

    $casosFiltrados[] = $caso;

    $estado = $caso['estado'] ?? '';
    if (isset($totalesEstado[$estado])) {
        $totalesEstado[$estado]++;
    }
}

$reporteAgrupado = [];
foreach ($casosFiltrados as $caso) {
    $clave = (string)($caso['asignado_a'] ?? 'sin_asignar');
    $nombre = trim((string)($caso['asignado_a_nombre'] ?? 'Sin asignar'));
    if ($nombre === '') {
        $nombre = 'Sin asignar';
    }

    if (!isset($reporteAgrupado[$clave])) {
        $reporteAgrupado[$clave] = [
            'nombre' => $nombre,
            'total' => 0,
            'Atendido' => 0,
            'Pendiente' => 0,
            'No atendido' => 0
        ];
    }

    $reporteAgrupado[$clave]['total']++;
    $estado = $caso['estado'] ?? '';
    if (isset($reporteAgrupado[$clave][$estado])) {
        $reporteAgrupado[$clave][$estado]++;
    }
}

usort($comisionados, function ($a, $b) {
    return strcasecmp($a['username'], $b['username']);
});

$reportePorComisionado = array_values($reporteAgrupado);
usort($reportePorComisionado, function ($a, $b) {
    return strcasecmp($a['nombre'], $b['nombre']);
});

$totalCasos = count($casosFiltrados);
$comisionadoSeleccionado = null;
foreach ($comisionados as $comisionado) {
    if ((string)$comisionado['id'] === (string)$filtro_comisionado) {
        $comisionadoSeleccionado = $comisionado;
        break;
    }
}

$fechaGeneracion = (new DateTime('now'))->format('d/m/Y H:i');
$desdeTexto = $fecha_inicio_dt ? $fecha_inicio_dt->format('d/m/Y') : 'el inicio del histórico';
$hastaTexto = $fecha_fin_dt ? $fecha_fin_dt->format('d/m/Y') : 'la fecha actual';
$rangoTexto = "Desde {$desdeTexto} hasta {$hastaTexto}";

if ($rol == 1) {
    require __DIR__ . '/../app/views/admin/reportes.php';
} else if ($rol == 2) {
    require __DIR__ . '/../app/views/comisionado/reportes.php';
} else {
    echo "Rol no permitido.";
    exit;
}
