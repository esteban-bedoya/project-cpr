<link rel="stylesheet" href="/project-cpr/public/assets/css/globals/caso.css">

<div class="case-layout">

            <!-- ======= SIDEBAR FILTROS ======= -->
            <div class="case-sidebar">

                <div class="filter-group">
                    <label class="filter-title">Comisionado</label>
                    <select>
                        <option>Marlery Gaviria</option>
                        <option>Comisionado 2</option>
                    </select>
                </div>

                <!-- TIPO DE CASO (RADIO) -->
                <div class="filter-group">
                    <label class="filter-title">Tipo de caso</label>

                    <label class="check-item">
                        <input type="radio" name="tipo_caso" checked>
                        <span>Denuncia</span>
                    </label>

                    <label class="check-item">
                        <input type="radio" name="tipo_caso">
                        <span>Solicitud</span>
                    </label>

                    <label class="check-item">
                        <input type="radio" name="tipo_caso">
                        <span>Derecho de petición</span>
                    </label>

                    <label class="check-item">
                        <input type="radio" name="tipo_caso">
                        <span>Tutela</span>
                    </label>
                </div>

                <!-- ESTADO (RADIO) -->
                <div class="filter-group">
                    <label class="filter-title">Estado</label>

                    <label class="check-item">
                        <input type="radio" name="estado" checked>
                        <span>Atendido</span>
                    </label>

                    <label class="check-item">
                        <input type="radio" name="estado">
                        <span>No atendido</span>
                    </label>

                    <label class="check-item">
                        <input type="radio" name="estado">
                        <span>Pendiente</span>
                    </label>
                </div>

                <div class="filter-group">
                    <label class="filter-title">Tipo de proceso</label>
                    <select>
                        <option>Uniformes</option>
                        <option>Presupuesto</option>
                        <option>Formación</option>
                    </select>
                </div>

                <button class="btn-actualizar">Actualizar</button>

            </div>

            <!-- ======= PANEL DE CASO ======= -->
            <div class="case-content">

                <div class="case-header"># 7399 | PRESUPUESTO FALTANTE</div>

                <div class="case-box">

                    <!-- Mensaje 1 -->
                    <div class="msg-entry">
                        <div class="msg-date">12/09/2025 16:40</div>
                        <div class="msg-user">Marlery Gaviria (Comisionado) agrega caso #7399 | PRESUPUESTO FALTANTE</div>

                        <div class="msg-body">
                            <strong>Kevin Gil Orlas (+57 3246765898 | kevin_go@gmail.com)</strong><br><br>
                            Se comunica el Sr. Kevin Gil Orlas por medio del correo electrónico.
                            Realiza una denuncia contra Jorge Álvarez, quien realizó un contrato
                            por una elevada suma incoherente. Adjunta evidencias.
                        </div>

                        <div class="msg-file">📎 Archivo adjunto</div>
                    </div>

                    <div class="divider"></div>

                    <!-- Mensaje 2 -->
                    <div class="msg-entry">
                        <div class="msg-date">13/09/2025 11:45</div>
                        <div class="msg-user">Marlery Gaviria (Comisionado)</div>

                        <div class="msg-status-change">
                            12/09/2025 16:40 — <strong>Marlery Gaviria cambió</strong><br>
                            de “No atendido” a “Pendiente”
                        </div>

                        <div class="msg-body">
                            Se registra el caso, me encuentro trabajando en él para brindar una pronta solución.
                        </div>
                    </div>

                </div>

                <!-- INPUT MENSAJE -->
                <div class="msg-input-box">
                    <input type="text" placeholder="Escribir ..." class="msg-input">

                    <label class="btn-attach">📎
                        <input type="file" hidden>
                    </label>

                    <button class="btn-enviar">Enviar</button>
                </div>

            </div>

        </div>