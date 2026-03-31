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
      <a href="../admin/reportes.php" aria-label="Ir a la página principal">
        <img src="/project-CPR/public/assets/img/logo-sena-cpr.png" alt="Logo SENA CPR">
      </a>
    </div>

    <!-- Navegación / Botones -->
    <nav class="nav-menu" aria-label="Menú de navegación">
      <a href="reportes.php" class="nav-item <?php echo ($activePage === 'reportes') ? 'active' : ''; ?>">Reportes</a>
      <a href="casos.php" class="nav-item <?php echo ($activePage === 'casos') ? 'active' : ''; ?>">Casos</a>
      <a href="usuarios.php" class="nav-item <?php echo ($activePage === 'usuarios') ? 'active' : ''; ?>">Usuarios</a>
      <a href="perfil.php" class="nav-item <?php echo ($activePage === 'perfil') ? 'active' : ''; ?>">
        Hola,<br>
        <strong><?php echo $_SESSION['usuario'] ?? 'Administrador'; ?></strong>
      </a>
    </nav>

  </header>
</div>
