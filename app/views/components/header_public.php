<!-- Componente: encabezado publico con logo y acceso al login -->
<link rel="stylesheet" href="/PROJECT-CPR/public/assets/css/globals/header_public.css"/>


<div class="header-background">
  <!-- Contenedor principal del header -->
  <header class="header">
    <!-- Logo y enlace a inicio -->
    <div class="logo"> <a href="index.php"> <img src="assets/img/logo-sena-cpr.png" alt="Logo SENA CPR"> </a> </div>
    <!-- Menu de navegacion con resaltado segun pagina activa -->
    <nav class="nav-menu"> <a href="login.php" class="nav-item <?php echo ($activePage === 'login') ? 'active' : ''; ?>">Ingresar</a> </nav>
  </header>
</div>
