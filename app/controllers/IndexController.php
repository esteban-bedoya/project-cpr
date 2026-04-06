<?php
// Controlador de pagina publica principal.

class IndexController
{
    // Punto de entrada para la ruta principal del sitio.
    public function index()
    {
        // La landing publica no necesita datos previos.
        require '../app/views/public/index.php';
    }
}
