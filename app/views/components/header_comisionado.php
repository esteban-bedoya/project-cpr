<!-- Componente: header para comisionado (menu principal de su rol) -->
<link rel="stylesheet" href="/PROJECT-CPR/public/assets/css/globals/header.css">

<?php 
// Bloque comentado: validacion de sesion que se maneja en los front controllers.
/*
if (!isset($_SESSION)) { session_start(); }
if (!isset($_SESSION['logged'])) {
    header("Location: login.php");
    exit;
}
*/
?>

<div class="header-background">
  <header class="header">

    <!-- Logo a la izquierda -->
    <div class="logo">
      <a href="gestionar.php" aria-label="Ir a la página principal">
        <img src="/PROJECT-CPR/public/assets/img/logo-sena-cpr.png" alt="Logo SENA CPR">
      </a>
    </div>


    <!-- Navegación / Botones -->
    <nav class="nav-menu" aria-label="Menú de navegación">
      <a href="/project-cpr/public/gestionar.php" class="nav-item <?php echo ($activePage === 'gestionar') ? 'active' : ''; ?>">Gestionar</a>
      <a href="/project-cpr/public/busqueda_caso.php" class="nav-item <?php echo ($activePage === 'busqueda') ? 'active' : ''; ?>">Búsqueda*</a>
      <a href="/project-cpr/public/reportes.php" class="nav-item <?php echo ($activePage === 'reportes') ? 'active' : ''; ?>">Reportes*</a>
      <a href="/project-cpr/public/perfil.php" class="nav-item <?php echo ($activePage === 'perfil') ? 'active' : ''; ?>">
        Comisionado,<br>
        <strong><?php echo $_SESSION['user']['username'] ?? 'Comisionado'; ?></strong>
      </a>
    </nav>

  </header>
</div>
