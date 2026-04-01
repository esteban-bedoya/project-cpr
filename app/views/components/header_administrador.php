<!-- Componente: header para administrador (menu del panel admin) -->
<link rel="stylesheet" href="/project-cpr/public/assets/css/globals/header.css">

<div class="header-background">
  <header class="header">

    <!-- Logo a la izquierda -->
    <div class="logo">
      <a href="/project-cpr/app/views/admin/reportes.php" aria-label="Ir a la página principal">
        <img src="/project-cpr/public/assets/img/logo-sena-cpr.png" alt="Logo SENA CPR">
      </a>
    </div>

    <!-- Navegación -->
    <nav class="nav-menu" aria-label="Menú de navegación">
      <a href="/project-cpr/public/reportes.php" class="nav-item <?= ($activePage === 'reportes') ? 'active' : ''; ?>">Reportes*</a>
      <a href="/project-cpr/public/busqueda_caso.php" class="nav-item <?= ($activePage === 'casos') ? 'active' : ''; ?>">Casos*</a>
      <a href="/project-cpr/public/usuarios.php" class="nav-item <?= ($activePage === 'usuarios') ? 'active' : ''; ?>">Usuarios</a>
      <a href="/project-cpr/public/perfil.php" class="nav-item <?= ($activePage === 'perfil') ? 'active' : ''; ?>">
        Administrador, <br>
        <strong><?= $_SESSION['user']['username'] ?? 'Administrador'; ?></strong>
      </a>
    </nav>

  </header>
</div>
