<?php
// Controlador de pagina publica principal.

class IndexController
{
    // Punto de entrada para la ruta principal del sitio.
    public function index()
    {
        // Carga la vista publica de inicio.
        require '../app/views/public/index.php';
    }
}
