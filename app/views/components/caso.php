<?php
// Prepara banderas simples para la vista.
$estadoActual = $caso['estado'] ?? 'Pendiente';
$fechaCierre = !empty($caso['fecha_cierre']) ? new DateTime($caso['fecha_cierre']) : null;
$hoy = new DateTime();
$estaVencido = $fechaCierre ? $fechaCierre < $hoy : false;

// Clases visuales por estado.
$estadoClases = [
    'Pendiente' => 'status-badge pendiente',
    'Atendido' => 'status-badge atendido',
    'No atendido' => 'status-badge no-atendido'
];
$estadoClase = $estadoClases[$estadoActual] ?? 'status-badge';
$casoEnGestion = $estadoActual === 'Pendiente';
$puedeEditarCaso = $usuarioPuedeEditar ?? false;
$puedeEditar = $puedeEditarCaso && $casoEnGestion && !$estaVencido;
$tituloSeguimiento = match ($estadoActual) {
    'Atendido' => 'Caso cerrado',
    'No atendido' => 'Fuera de gestión',
    default => 'Aún dentro del plazo'
};
$mostrarBotonAtender = $puedeEditarCaso && $estadoActual === 'Pendiente';
$mostrarBotonReabrir = $puedeEditarCaso && in_array($estadoActual, ['Atendido', 'No atendido'], true);
$mostrarFechaCierre = $fechaCierre !== null;
$fechaCierreTexto = $fechaCierre ? $fechaCierre->format('d/m/Y H:i') : 'Sin definir';
$estadoSeguimientoTexto = match ($estadoActual) {
    'Atendido' => 'El caso se encuentra fuera de gestión porque ya fue atendido.',
    'No atendido' => 'El sistema cerró el caso porque no recibió atención dentro del plazo establecido.',
    default => 'El caso sigue en gestión y el encargado puede continuar con su seguimiento.'
};

$actividad = [];

// A partir de aquí la vista deja de pensar en tablas separadas
// y arma una sola colección de "actividad" para renderizar todo
// con el mismo formato.
// Primero se traducen los cambios de estado a un formato común.
foreach ($historial as $item) {
    $usuarioActividad = $item['username'];
    if (
        !empty($item['descripcion']) &&
        str_contains($item['descripcion'], 'Cambio de estado automático del sistema de Pendiente a No atendido')
    ) {
        $usuarioActividad = $caso['asignado_a_nombre'] ?? $item['username'];
    }

    $actividad[] = [
        'tipo' => 'estado',
        'fecha' => $item['fecha'],
        'titulo' => 'Cambio de estado',
        'descripcion' => $item['descripcion'],
        'usuario' => $usuarioActividad
    ];
}

// Luego se convierten los cambios puntuales de campos
// (radicado, fecha, etc.) para que entren a la misma línea de tiempo.
foreach ($historialCampos as $item) {
    $labelsCampos = [
        'radicado_sena' => 'Radicado SENA',
        'asunto' => 'Asunto',
        'detalles' => 'Detalles del caso',
        'fecha_cierre' => 'Fecha de cierre'
    ];

    $campoLabel = $labelsCampos[$item['campo']] ?? $item['campo'];
    $descripcion = $item['campo'] === 'radicado_sena'
        ? 'Cambio de Radicado SENA'
        : 'Cambio en ' . $campoLabel;
    $detalleCambio = $item['campo'] === 'radicado_sena'
        ? 'Cambio de Radicado SENA de ' . ($item['valor_anterior'] ?? 'Sin registrar') . ' a ' . ($item['valor_nuevo'] ?? 'Sin registrar')
        : 'De "' . ($item['valor_anterior'] ?? '') . '" a "' . ($item['valor_nuevo'] ?? '') . '"';

    $actividad[] = [
        'tipo' => 'campo',
        'fecha' => $item['fecha'],
        'titulo' => $descripcion,
        'descripcion' => $detalleCambio,
        'usuario' => $item['username']
    ];
}

// Esta lista se ordena aparte porque todavía no incluye los mensajes.
usort($actividad, fn($a, $b) => strtotime($b['fecha']) <=> strtotime($a['fecha']));

$lineaTiempo = [];

// La conversación del caso y la auditoría de cambios se muestran juntas,
// así que ambos bloques terminan viviendo en la misma línea de tiempo.
foreach ($mensajes as $mensaje) {
    $lineaTiempo[] = [
        'tipo' => 'mensaje',
        'fecha' => $mensaje['fecha'],
        'usuario' => $mensaje['username'],
        'mensaje' => $mensaje['mensaje'] ?? '',
        'archivo' => $mensaje['archivo'] ?? ''
    ];
}

foreach ($actividad as $item) {
    $lineaTiempo[] = [
        'tipo' => 'actividad',
        'fecha' => $item['fecha'],
        'usuario' => $item['usuario'],
        'titulo' => $item['titulo'],
        'descripcion' => $item['descripcion']
    ];
}

// El render final se deja en orden cronológico para que se lea
// como una conversación: lo más viejo arriba y lo más nuevo al final.
usort($lineaTiempo, fn($a, $b) => strtotime($a['fecha']) <=> strtotime($b['fecha']));
?>

<link rel="stylesheet" href="/project-cpr/public/assets/css/globals/caso.css">

<div class="case-layout">
    <aside class="case-sidebar">
        <div class="case-summary-card <?= $estaVencido ? 'is-expired' : '' ?>">
            <h2>Estado del caso</h2>
            <span class="<?= $estadoClase ?>"><?= htmlspecialchars($estadoActual) ?></span>
            <span class="case-state-label"><?= htmlspecialchars($tituloSeguimiento) ?></span>
            <p><?= htmlspecialchars($estadoSeguimientoTexto) ?></p>

            <?php if ($mostrarFechaCierre): ?>
                <div class="case-date-box">
                    <span class="case-date-label">Fecha de cierre</span>
                    <strong><?= htmlspecialchars($fechaCierreTexto) ?></strong>
                </div>
            <?php endif; ?>

            <?php if ($mostrarBotonAtender): ?>
                <div class="case-primary-action">
                    <form method="POST" action="/project-cpr/public/caso.php">
                        <!-- Se envían también los valores actuales para no perder
                             tipo de caso ni tipo de proceso al cambiar el estado. -->
                        <input type="hidden" name="action" value="updateDetalle">
                        <input type="hidden" name="caso_id" value="<?= $caso['id'] ?>">
                        <input type="hidden" name="tipo_caso_id" value="<?= htmlspecialchars((string)($caso['tipo_caso_id'] ?? '')) ?>">
                        <input type="hidden" name="tipo_proceso_id" value="<?= htmlspecialchars((string)($caso['tipo_proceso_id'] ?? '')) ?>">
                        <input type="hidden" name="estado" value="Atendido">
                        <button type="submit" class="btn-primary-case-action">Marcar caso como atendido</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if ($mostrarBotonReabrir): ?>
                <div class="case-primary-action">
                    <button type="button" class="btn-primary-case-action reopen-toggle">Reabrir caso</button>
                </div>

                <!-- La reapertura no cambia todo el caso:
                     solo fuerza una nueva fecha de cierre para devolverlo a gestión. -->
                <form method="POST" action="/project-cpr/public/caso.php" class="reopen-form">
                    <input type="hidden" name="action" value="updateCampos">
                    <input type="hidden" name="caso_id" value="<?= $caso['id'] ?>">
                    <input type="hidden" name="radicado_sena" value="<?= htmlspecialchars($caso['radicado_sena'] ?? '') ?>">

                    <label class="filter-title" for="fecha_cierre_reapertura">Fecha de cierre</label>
                    <input
                        type="date"
                        id="fecha_cierre_reapertura"
                        name="fecha_cierre"
                        class="reopen-date-input"
                        value="<?= !empty($caso['fecha_cierre']) ? date('Y-m-d', strtotime($caso['fecha_cierre'])) : '' ?>"
                        min="<?= date('Y-m-d', strtotime('+1 day')) ?>">

                    <?php if (isset($_SESSION['error']) && ($_GET['error'] ?? '') === 'fechacierre'): ?>
                        <p class="feedback error"><?= $_SESSION['error']; ?></p>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <button type="submit" class="btn-secondary-case-action">Guardar fecha de cierre</button>
                </form>
            <?php endif; ?>
        </div>

        <div class="case-sidebar-card">
            <div class="sidebar-section-title">Datos del caso</div>

            <!-- Este formulario se envía solo cuando cambian los selects.
                 La idea es que la edición lateral se sienta rápida,
                 sin necesitar un botón adicional para guardar. -->
            <form method="POST" action="/project-cpr/public/caso.php" class="case-actions-form">
                <input type="hidden" name="action" value="updateDetalle">
                <input type="hidden" name="caso_id" value="<?= $caso['id'] ?>">
                <input type="hidden" name="estado" value="<?= htmlspecialchars($estadoActual) ?>">

                <div class="filter-group">
                    <label class="filter-title" for="tipo_caso_id">Tipo de caso</label>
                    <!-- El select se deshabilita visualmente, pero la validación fuerte
                         sigue estando en el controlador por seguridad. -->
                    <select id="tipo_caso_id" name="tipo_caso_id" <?= !$puedeEditar ? 'disabled' : '' ?>>
                        <?php foreach (($tiposCaso ?? []) as $tipoCaso): ?>
                            <option
                                value="<?= $tipoCaso['id'] ?>"
                                <?= (string)($caso['tipo_caso_id'] ?? '') === (string)$tipoCaso['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tipoCaso['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-title" for="tipo_proceso_id">Tipo de proceso</label>
                    <select id="tipo_proceso_id" name="tipo_proceso_id" <?= !$puedeEditar ? 'disabled' : '' ?>>
                        <?php foreach (($tiposProceso ?? []) as $tipoProceso): ?>
                            <?php $sufijoInactivo = !empty($tipoProceso['_inactivo']) ? ' (Inactivo)' : ''; ?>
                            <option
                                value="<?= $tipoProceso['id'] ?>"
                                <?= (string)($caso['tipo_proceso_id'] ?? '') === (string)$tipoProceso['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tipoProceso['nombre']) ?><?= $sufijoInactivo ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>

    </aside>

    <div class="case-content">
        <div class="case-header">
            #<?= $caso['numero_caso'] ?> | <?= htmlspecialchars($caso['asunto']) ?>
        </div>

        <div class="case-info">
            <?php if (isset($_SESSION['success'])): ?>
                <p class="feedback success"><?= $_SESSION['success']; ?></p>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error']) && ($_GET['error'] ?? '') !== 'fechacierre'): ?>
                <p class="feedback error"><?= $_SESSION['error']; ?></p>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="case-overview">
                <!-- Este bloque resume lo esencial del caso.
                     El único dato editable aquí se dejó como edición puntual:
                     el radicado SENA. -->
                <div class="summary-field summary-field-editable">
                    <span>Radicado SENA</span>
                    <div class="summary-field-header">
                        <strong><?= !empty($caso['radicado_sena']) ? htmlspecialchars($caso['radicado_sena']) : 'No registrado' ?></strong>
                        <?php if ($puedeEditarCaso): ?>
                            <!-- El radicado quedó como edición puntual para evitar
                                 llenar la vista con más formularios visibles. -->
                            <button type="button" class="btn-summary-edit" id="toggle-radicado">Editar</button>
                        <?php endif; ?>
                    </div>

                    <?php if ($puedeEditarCaso): ?>
                        <form method="POST" action="/project-cpr/public/caso.php" class="summary-edit-form" id="form-radicado">
                            <input type="hidden" name="action" value="updateCampos">
                            <input type="hidden" name="caso_id" value="<?= $caso['id'] ?>">
                            <input type="text" name="radicado_sena" maxlength="10" value="<?= htmlspecialchars($caso['radicado_sena'] ?? '') ?>" class="summary-edit-input" placeholder="Escribe el radicado">
                            <button type="submit" class="btn-summary-save">Guardar</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="summary-field">
                    <span>Creado por</span>
                    <strong><?= htmlspecialchars($caso['asignado_a_nombre'] ?? 'Sin asignar') ?></strong>
                </div>
                <div class="summary-field">
                    <span>Fecha de creación</span>
                    <strong><?= !empty($caso['fecha_creacion']) ? date('d/m/Y H:i', strtotime($caso['fecha_creacion'])) : 'No registrada' ?></strong>
                </div>
            </div>

            <?php if (!empty($caso['detalles'])): ?>
                <div class="info-item info-detalles">
                    <strong>Detalles del caso</strong><br>
                    <?= nl2br(htmlspecialchars($caso['detalles'])) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="case-box">
            <div class="section-heading">
                <h3>Historial del caso: Acá puedes visualizar su trazabilidad</h3>
            </div>

            <div class="case-messages">
                <!-- Cada evento ya viene normalizado en $lineaTiempo.
                     Aquí solo se decide si se pinta como mensaje
                     o como movimiento del historial. -->
                <?php if (!empty($lineaTiempo)): ?>
                    <?php foreach ($lineaTiempo as $evento): ?>
                        <div class="msg-entry timeline-entry">
                            <div class="msg-date">
                                <?= date('d/m/Y H:i', strtotime($evento['fecha'])) ?>
                            </div>

                            <div class="msg-user">
                                <?= htmlspecialchars($evento['usuario']) ?>
                            </div>

                            <?php if ($evento['tipo'] === 'mensaje'): ?>
                                <div class="activity-card">
                                    <strong>Mensaje del caso</strong>
                                    <?php if (!empty($evento['mensaje'])): ?>
                                        <p><?= nl2br(htmlspecialchars($evento['mensaje'])) ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($evento['archivo'])): ?>
                                        <div class="msg-file">
                                            <a href="/project-cpr/public/uploads/casos/<?= htmlspecialchars($evento['archivo']) ?>" target="_blank">
                                                Ver archivo adjunto
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="activity-card">
                                    <strong>Actualización del caso</strong>
                                    <p><?= htmlspecialchars($evento['descripcion']) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="divider"></div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-state">Aún no hay movimientos registrados en este caso.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($puedeEditarCaso): ?>
            <!-- La caja inferior funciona como entrada rápida del caso:
                 permite mandar solo mensaje, solo archivo o ambos. -->
            <form
                class="msg-input-box"
                method="POST"
                enctype="multipart/form-data"
                action="/project-cpr/public/caso.php">

                <input type="hidden" name="action" value="mensaje">
                <input type="hidden" name="caso_id" value="<?= $caso['id'] ?>">

                <input
                    type="text"
                    name="mensaje"
                    placeholder="Escribir mensaje y/o adjuntar un archivo"
                    class="msg-input"
                    <?= !$puedeEditar ? 'disabled' : '' ?>>

                <label class="btn-attach <?= !$puedeEditar ? 'is-disabled' : '' ?>" for="archivo-caso">
                    Archivo
                    <input
                        id="archivo-caso"
                        type="file"
                        name="archivo"
                        accept=".pdf,.jpg,.jpeg,.png"
                        hidden
                        <?= !$puedeEditar ? 'disabled' : '' ?>>
                </label>

                <span class="file-name" id="archivo-nombre">Sin archivo</span>

                <button class="btn-enviar" <?= !$puedeEditar ? 'disabled' : '' ?>>Enviar</button>
            </form>

            <?php if (isset($_GET['error']) && in_array($_GET['error'], ['vacio', 'tipo', 'tamano', 'subida'], true)): ?>
                <p class="feedback error">
                    <?php
                    echo match ($_GET['error']) {
                        'vacio' => 'Debes escribir un mensaje o adjuntar un archivo.',
                        'tipo' => 'El archivo no es valido. Solo se permiten PDF, JPG, JPEG y PNG.',
                        'tamano' => 'El archivo supera el tamaño permitido.',
                        'subida' => 'No se pudo subir el archivo. Intenta nuevamente.',
                    };
                    ?>
                </p>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>

<script>
    window.addEventListener('load', () => {
        const messages = document.querySelector('.case-messages');
        if (messages) {
            // Al abrir el detalle, baja automáticamente al final
            // para mostrar lo más reciente del historial.
            messages.scrollTop = messages.scrollHeight;
        }
    });
</script>

<script>
    // Este bloque reúne pequeñas interacciones de la vista:
    // autosubmit de selects, toggle de reapertura, nombre del archivo
    // y apertura puntual del formulario de radicado.
    const tipoCasoSelect = document.getElementById('tipo_caso_id');
    const tipoProcesoSelect = document.getElementById('tipo_proceso_id');
    const reopenToggle = document.querySelector('.reopen-toggle');
    const reopenForm = document.querySelector('.reopen-form');
    const reopenDateInput = document.querySelector('.reopen-date-input');
    const radicadoToggle = document.getElementById('toggle-radicado');
    const radicadoForm = document.getElementById('form-radicado');
    const archivoInput = document.getElementById('archivo-caso');
    const archivoNombre = document.getElementById('archivo-nombre');
    const edicionBloqueada = <?= $puedeEditar ? 'false' : 'true' ?>;

    [tipoCasoSelect, tipoProcesoSelect].forEach((select) => {
        if (!select) return;
        select.addEventListener('change', () => {
            if (edicionBloqueada || select.disabled) return;
            // Cuando cambia uno de los select laterales,
            // se envía su mismo formulario sin esperar otro botón.
            const form = select.closest('form');
            if (form) {
                form.submit();
            }
        });
    });

    if (reopenToggle && reopenForm && reopenDateInput) {
        reopenToggle.addEventListener('click', () => {
            reopenForm.classList.toggle('is-open');
            if (reopenForm.classList.contains('is-open')) {
                // Si el navegador lo soporta, abre directamente el picker de fecha.
                reopenDateInput.focus();
                if (typeof reopenDateInput.showPicker === 'function') {
                    reopenDateInput.showPicker();
                }
            }
        });

        if (<?= (($_GET['error'] ?? '') === 'fechacierre' && $mostrarBotonReabrir) ? 'true' : 'false' ?>) {
            reopenForm.classList.add('is-open');
        }
    }

    if (archivoInput && archivoNombre) {
        archivoInput.addEventListener('change', () => {
            const archivo = archivoInput.files && archivoInput.files[0];
            archivoNombre.textContent = archivo ? archivo.name : 'Sin archivo';
        });
    }

    if (radicadoToggle && radicadoForm) {
        radicadoToggle.addEventListener('click', () => {
            // El radicado se deja escondido hasta que el usuario decide editarlo.
            radicadoForm.classList.toggle('is-open');
        });
    }

</script>
