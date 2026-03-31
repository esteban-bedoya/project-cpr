<link rel="stylesheet" href="/PROJECT-CPR/public/assets/css/globals/header.css">

<?php 
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
      <a href="gestionar.php" class="nav-item <?php echo ($activePage === 'gestionar') ? 'active' : ''; ?>">Gestionar</a>
      <a href="busqueda.php" class="nav-item <?php echo ($activePage === 'busqueda') ? 'active' : ''; ?>">Búsqueda</a>
      <a href="reportes.php" class="nav-item <?php echo ($activePage === 'reportes') ? 'active' : ''; ?>">Reportes</a>
      <a href="perfil.php" class="nav-item <?php echo ($activePage === 'perfil') ? 'active' : ''; ?>">
        Hola,<br>
        <strong><?php echo $_SESSION['usuario'] ?? 'Comisionado'; ?></strong>
      </a>
    </nav>

  </header>
</div>
