<?php
$page_title = "Panel de Administración";
require_once 'includes/header.php';

// Restrict access to administrators
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'pedidos';
$secciones = [
    'pedidos' => 'Gestionar Pedidos',
    'usuarios' => 'Gestionar Usuarios'
];
?>

<main class="py-16 bg-warm-bg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-left mb-12">
            <h1 class="font-heading text-4xl font-bold text-text-dark mb-2">Panel de Administración</h1>
            <p class="text-xl text-gray-600">Aquí puedes gestionar pedidos y usuarios.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-8" aria-label="Tabs">
                    <?php foreach ($secciones as $key => $value): ?>
                        <a href="?seccion=<?php echo $key; ?>"
                           class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm
                                  <?php echo $seccion === $key
                                      ? 'border-primary text-primary'
                                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                            <?php echo $value; ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

            <div class="p-8">
                <?php
                $include_file = 'includes/admin_' . $seccion . '.php';
                if (file_exists($include_file)) {
                    include $include_file;
                } else {
                    echo "<p>Contenido no encontrado.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>
