<?php
$page_title = "Panel de Administración";
require_once 'includes/header.php';

// Restrict access to administrators
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>

<main class="py-16 bg-warm-bg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-left mb-12">
            <h1 class="font-heading text-4xl font-bold text-text-dark mb-2">Panel de Administración</h1>
            <p class="text-xl text-gray-600">Bienvenido al panel de administración.</p>
        </div>
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>
