<div class="main-content">
        <div class="search-container">
            
            <!-- =========================================================== -->
            <!-- ==================== SIDEBAR DE FILTROS =================== -->
            <!-- =========================================================== -->
            <aside class="filters-sidebar">
                <h2 class="filters-title">Filtros</h2>

                <!-- Filtro de Rango de fechas -->
                <div class="filter-group">
                    <label class="filter-label">Rango de fechas</label>
                    <input type="text" class="date-input" placeholder="dd/mm/aa - dd/mm/aa">
                </div>

                <!-- Filtro de Tipo de caso -->
                <div class="filter-group">
                    <label class="filter-label filter-toggle">
                        Tipo de caso
                        <span class="toggle-icon">∨</span>
                    </label>
                    <div class="filter-options">
                        <label class="checkbox-label selected">
                            <input type="checkbox" checked>
                            <span class="checkbox-custom"></span>
                            Denuncia
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox">
                            <span class="checkbox-custom"></span>
                            Solicitud
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox">
                            <span class="checkbox-custom"></span>
                            Derecho de petición
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox">
                            <span class="checkbox-custom"></span>
                            Tutela
                        </label>
                    </div>
                </div>

                <!-- Filtro de Estado -->
                <div class="filter-group">
                    <label class="filter-label filter-toggle">
                        Estado
                        <span class="toggle-icon">∨</span>
                    </label>
                    <div class="filter-options">
                        <label class="checkbox-label selected">
                            <input type="checkbox" checked>
                            <span class="checkbox-custom"></span>
                            Atendido
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox">
                            <span class="checkbox-custom"></span>
                            No atendido
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox">
                            <span class="checkbox-custom"></span>
                            Pendiente
                        </label>
                    </div>
                </div>

                <!-- Filtro de Tipo de proceso -->
                <div class="filter-group">
                    <label class="filter-label filter-toggle">
                        Tipo de proceso
                        <span class="toggle-icon">∧</span>
                    </label>
                    <div class="filter-options collapsed">
                        <!-- Opciones colapsadas por defecto -->
                    </div>
                </div>

                <!-- Filtro de Comisionado(s) -->
                <div class="filter-group">
                    <label class="filter-label filter-toggle">
                        Comisionado(s)
                        <span class="toggle-icon">∧</span>
                    </label>
                    <input type="text" class="text-input" placeholder="...">
                </div>
            </aside>

            <!-- =========================================================== -->
            <!-- ============== ÁREA DE BÚSQUEDA Y RESULTADOS ============== -->
            <!-- =========================================================== -->
            <section class="search-main">
                <!-- Barra de búsqueda -->
                <div class="search-bar">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-input" placeholder="Buscar">
                </div>

                <!-- Tabla de resultados -->
                <div class="results-table">
                    <table class="cases-table">
                        <tbody>
                            <!-- Aquí irían los resultados dinámicos desde la base de datos -->
                            <tr class="table-row">
                                <td class="col-id">7399</td>
                                <td class="col-description">Presupuesto faltante</td>
                                <td class="col-date">11-11-2025</td>
                                <td class="col-type">Denuncia</td>
                            </tr>
                            <tr class="table-row">
                                <td class="col-id">3267</td>
                                <td class="col-description">Profesionales faltantes</td>
                                <td class="col-date">03-11-2025</td>
                                <td class="col-type">Denuncia</td>
                            </tr>
                            <tr class="table-row">
                                <td class="col-id">2789</td>
                                <td class="col-description">Poco presupuesto</td>
                                <td class="col-date">09-10-2025</td>
                                <td class="col-type">Denuncia</td>
                            </tr>
                            <tr class="table-row">
                                <td class="col-id">1245</td>
                                <td class="col-description">Gastos inflados</td>
                                <td class="col-date">03-09-2025</td>
                                <td class="col-type">Denuncia</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>
    </div>

    <!-- =========================================================== -->
    <!-- ======================= JAVASCRIPT ======================== -->
    <!-- =========================================================== -->
    <script>
        // Toggle de filtros colapsables
        document.querySelectorAll('.filter-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const icon = this.querySelector('.toggle-icon');
                const options = this.parentElement.querySelector('.filter-options');
                
                if (options) {
                    // Alternar clase 'collapsed' para mostrar/ocultar opciones
                    options.classList.toggle('collapsed');
                    // Cambiar el icono según el estado
                    icon.textContent = options.classList.contains('collapsed') ? '∧' : '∨';
                }
            });
        });

        // Funcionalidad de checkboxes personalizados
        document.querySelectorAll('.checkbox-label').forEach(label => {
            label.addEventListener('click', function(e) {
                // Evitar que el click en el input dispare dos veces
                if (e.target.tagName !== 'INPUT') {
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    checkbox.checked = !checkbox.checked;
                }
                // Alternar clase 'selected' para el estilo visual
                this.classList.toggle('selected');
            });
        });
    </script>

</body>

</html>
