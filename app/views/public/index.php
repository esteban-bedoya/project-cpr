<?php $activePage = 'index'; ?>
<!-- Vista publica de inicio (landing page) -->


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sistema de Gestión - CPR</title>
<link rel="stylesheet" href="assets/css/globals/base.css" />
<link rel="stylesheet" href="assets/css/globals/index.css" />

</head>

<body class="public">

  <!-- Header publico -->
  <?php include("../app/views/components/header_public.php"); ?>

  <main>
    <!-- Seccion principal con titulo -->
    <section class="hero">
      <div class="hero-content">
        <h1>SISTEMA DE GESTIÓN <span>COMISIÓN DE PERSONAL</span></h1>
      </div>
    </section>

    <!-- Texto institucional -->
    <section class="info">
      <p>
        Órgano de participación conformado por representantes de los servidores públicos y de la administración.<br>
        En este espacio se consolidan los casos relacionados con presuntas irregularidades,<br>
        con el propósito de velar por la transparencia, la equidad y la ética institucional.
      </p>
    </section>

    <!-- Franja decorativa -->
    <section class="color_azul"></section>

  </main>



  <!-- Footer compartido -->
  <?php include('../app/views/components/footer.php'); ?>

</body>

</html>
