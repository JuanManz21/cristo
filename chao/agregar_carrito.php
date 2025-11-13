<?php
session_start();
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'])) {
    $producto_id = (int)$_POST['producto_id'];
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

    // Inicializar carrito si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = array();
    }

    // Agregar o actualizar producto en carrito
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id] += $cantidad;
    } else {
        $_SESSION['carrito'][$producto_id] = $cantidad;
    }

    // Mensaje de confirmación
    $_SESSION['mensaje'] = 'Producto agregado al carrito exitosamente.';

    // Redireccionar de vuelta
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'productos.php';
    header("Location: $redirect");
    exit();
}

header('Location: productos.php');
exit();
?>
