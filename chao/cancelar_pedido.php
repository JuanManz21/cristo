<?php
session_start();
require_once 'config/database.php';
require_once 'models/Pedido.php';
require_once 'models/Usuario.php';

// Restrict access to administrators
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_pedido']) && isset($_POST['id_usuario']) && isset($_POST['total'])) {
        $database = new Database();
        $conn = $database->getConnection();

        $pedido = new Pedido($conn);
        $pedido->id_pedido = $_POST['id_pedido'];
        $pedido->estado = 'cancelado';

        $usuario = new Usuario($conn);
        $usuario->id_usuario = $_POST['id_usuario'];

        // Start a transaction
        $conn->beginTransaction();

        try {
            // Update the order status
            $pedido->updateStatus();

            // Update the user's balance
            $usuario->updateSaldo($_POST['total']);

            // Commit the transaction
            $conn->commit();

            header('Location: admin.php?seccion=pedidos&mensaje=Pedido cancelado y saldo devuelto.');
            exit();

        } catch (Exception $e) {
            // Rollback the transaction if something failed
            $conn->rollBack();
            header('Location: admin.php?seccion=pedidos&error=Error al cancelar el pedido.');
            exit();
        }
    } else {
        header('Location: admin.php?seccion=pedidos&error=Datos incompletos.');
        exit();
    }
} else {
    header('Location: admin.php?seccion=pedidos');
    exit();
}
?>
