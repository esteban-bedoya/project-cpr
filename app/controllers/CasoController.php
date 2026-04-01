<?php
require_once __DIR__ . '/../models/Caso.php';

class CasoController
{
    // ===============================
    // MOSTRAR TODOS LOS CASOS
    // ===============================
    public function index()
    {
        session_start();
        if (!isset($_SESSION['logged'])) header("Location: /project-cpr/public/login.php");

        $rol = $_SESSION['user']['rol'];
        if (!in_array($rol, [1,2])) header("Location: /project-cpr/public/login.php");

        $casos = Caso::all();

        if ($rol == 1) {
            require __DIR__ . '/../views/admin/casos.php';
        } else {
            require __DIR__ . '/../views/comisionado/casos.php';
        }
    }

    // ===============================
    // MOSTRAR UN CASO
    // ===============================
    public function show($id)
    {
        session_start();
        if (!isset($_SESSION['logged'])) header("Location: /project-cpr/public/login.php");

        $rol = $_SESSION['user']['rol'];
        if (!in_array($rol, [1,2])) header("Location: /project-cpr/public/login.php");

        $caso = Caso::find($id);
        if (!$caso) header("Location: /project-cpr/casos");

        $tiposCaso = Caso::getTiposCaso();
        $tiposProceso = Caso::getTiposProceso();
        $historial = Caso::getHistorial($id);
        $mensajes = Caso::getMensajes($id);

        require __DIR__ . '/../views/caso.php';
    }

    // ===============================
    // CREAR CASO
    // ===============================
    public function store()
    {
        session_start();
        if (!isset($_SESSION['logged'])) header("Location: /project-cpr/public/login.php");

        $data = [
            'tipo_caso_id'       => $_POST['tipo_caso_id'] ?? null,
            'tipo_proceso_id'    => $_POST['tipo_proceso_id'] ?? null,
            'demandante_nombre'  => $_POST['demandante_nombre'] ?? null,
            'demandante_contacto'=> $_POST['demandante_contacto'] ?? null,
            'asunto'             => $_POST['asunto'] ?? null,
            'detalles'           => $_POST['detalles'] ?? null,
            'estado'             => $_POST['estado'] ?? 'No atendido',
            'creado_por'         => $_SESSION['user']['id']
        ];

        Caso::create($data);
        header("Location: /project-cpr/casos");
    }

    // ===============================
    // ACTUALIZAR CASO
    // ===============================
    public function update()
    {
        session_start();
        if (!isset($_SESSION['logged'])) header("Location: /project-cpr/public/login.php");

        $id = $_POST['id'] ?? null;
        if (!$id) header("Location: /project-cpr/casos");

        $data = [
            'tipo_caso_id'       => $_POST['tipo_caso_id'] ?? null,
            'tipo_proceso_id'    => $_POST['tipo_proceso_id'] ?? null,
            'demandante_nombre'  => $_POST['demandante_nombre'] ?? null,
            'demandante_contacto'=> $_POST['demandante_contacto'] ?? null,
            'asunto'             => $_POST['asunto'] ?? null,
            'detalles'           => $_POST['detalles'] ?? null,
            'estado'             => $_POST['estado'] ?? null
        ];

        Caso::update($id, $data);
        header("Location: /project-cpr/casos");
    }

    // ===============================
    // ELIMINAR CASO
    // ===============================
    public function delete()
    {
        session_start();
        if (!isset($_SESSION['logged'])) header("Location: /project-cpr/public/login.php");

        $id = $_POST['id'] ?? null;
        if ($id) Caso::delete($id);

        header("Location: /project-cpr/casos");
    }
}
