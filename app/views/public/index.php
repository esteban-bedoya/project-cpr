<?php $activePage = 'index'; ?>


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

  <?php include("../app/views/components/header_public.php"); ?>

  <main>
    <section class="hero">
      <div class="hero-content">
        <h1>SISTEMA DE GESTIÓN</h1>
        <h2>COMISIÓN DE PERSONAL</h2>
      </div>
    </section>

    <section class="info">
      <p>
        ÓRGANO DE PARTICIPACIÓN CONFORMADO POR REPRESENTANTES DE LOS SERVIDORES PÚBLICOS Y DE LA ADMINISTRACIÓN.<br>
        EN ESTE ESPACIO SE CONSOLIDAN LOS CASOS RELACIONADOS CON PRESUNTAS IRREGULARIDADES,<br>
        CON EL PROPÓSITO DE VELAR POR LA TRANSPARENCIA, LA EQUIDAD Y LA ÉTICA INSTITUCIONAL.
      </p>
    </section>

    <section class="color_azul"></section>

  </main>



  <?php include('../app/views/components/footer.php'); ?>

</body>

</html>