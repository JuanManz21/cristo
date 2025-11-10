<?php
session_start();
require_once 'config/database.php';
require_once 'models/Usuario.php';

// Restrict access to administrators
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_usuario'])) {
        $database = new Database();
        $conn = $database->getConnection();

        $usuario = new Usuario($conn);
        $usuario->id_usuario = $_POST['id_usuario'];
        $usuario->estado = 'inactivo';

        if ($usuario->updateEstado()) {
            header('Location: admin.php?seccion=usuarios&mensaje=Usuario desactivado con éxito.');
            exit();
        } else {
            header('Location: admin.php?seccion=usuarios&error=Error al desactivar el usuario.');
            exit();
        }
    } else {
        header('Location: admin.php?seccion=usuarios&error=Datos incompletos.');
        exit();
    }
} else {
    header('Location: admin.php?seccion=usuarios');
    exit();
}
?>
