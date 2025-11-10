<?php
session_start();
require_once 'config/database.php';
require_once 'models/Producto.php';

// Setup database connection
$database = new Database();
$conn = $database->getConnection();

// Instantiate the product model for general use
$producto_model = new Producto($conn);

// Get category data for navigation/footer links
$categorias_header = $producto_model->obtenerCategorias();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'LIVE - Postres con Propósito'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Source+Sans+Pro:wght@300;400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'heading': ['Playfair Display', 'serif'],
                        'body': ['Source Sans Pro', 'sans-serif']
                    },
                    colors: {
                        'primary': '#15803d',
                        'secondary': '#84cc16',
                        'accent': '#22c55e',
                        'warm-bg': '#f0fdf4',
                        'text-dark': '#374151'
                    }
                }
            }
        }
    </script>
</head>
<body class="font-body bg-warm-bg text-text-dark">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="index.php" class="font-heading text-2xl font-bold text-primary">LIVE</a>
                    <span class="ml-2 text-sm text-gray-600">Postres con Propósito</span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-text-dark hover:text-primary transition-colors">Inicio</a>
                    <a href="productos.php" class="text-text-dark hover:text-primary transition-colors">Productos</a>
                    <a href="apoyo-emocional.php" class="text-text-dark hover:text-primary transition-colors">Apoyo Emocional</a>
                    <a href="carrito.php" class="text-text-dark hover:text-primary transition-colors">Carrito</a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <?php if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'admin'): ?>
                            <a href="admin.php" class="bg-secondary text-white px-4 py-2 rounded-lg hover:bg-yellow-500 transition-colors">Panel Admin</a>
                        <?php endif; ?>
                        <a href="dashboard.php" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-green-800 transition-colors">Mi Cuenta</a>
                        <a href="logout.php" class="text-text-dark hover:text-primary transition-colors">Cerrar Sesión</a>
                    <?php else: ?>
                        <a href="login.php" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-green-800 transition-colors">Iniciar Sesión</a>
                        <a href="registro.php" class="text-text-dark hover:text-primary transition-colors">Registro</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
