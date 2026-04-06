<!-- Componente: header para comisionado (menu principal de su rol) -->
<link rel="stylesheet" href="/PROJECT-CPR/public/assets/css/globals/header.css">

<?php 
// La sesion se valida antes de cargar este componente.
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
      <a href="casos.php" aria-label="Ir a la página principal">
        <img src="/PROJECT-CPR/public/assets/img/logo-sena-cpr.png" alt="Logo SENA CPR">
      </a>
    </div>


    <!-- Menu visible del comisionado -->
    <nav class="nav-menu" aria-label="Menú de navegación">
    <!--   <a href="/project-cpr/public/casos.php" class="nav-item <?php echo ($activePage === 'gestionar') ? 'active' : ''; ?>">Gestionar</a> -->
      <a href="/project-cpr/public/casos.php" class="nav-item <?php echo ($activePage === 'casos') ? 'active' : ''; ?>">Casos</a>
      <a href="/project-cpr/public/reportes.php" class="nav-item <?php echo ($activePage === 'reportes') ? 'active' : ''; ?>">Reportes</a>
      <a href="/project-cpr/public/perfil.php" class="nav-item <?php echo ($activePage === 'perfil') ? 'active' : ''; ?>">
        Comisionado,<br>
        <strong><?php echo $_SESSION['user']['username'] ?? 'Comisionado'; ?></strong>
      </a>
    </nav>

  </header>
</div>
