<?php
$page_title = "Panel de Administración";
require_once 'includes/header.php';
require_once 'models/Usuario.php';
require_once 'models/Pedido.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'pedidos';
$secciones_admin = [
    'pedidos' => 'Últimos Pedidos',
    'usuarios' => 'Gestionar Usuarios'
];
?>

<main class="py-16 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="font-heading text-4xl font-bold text-text-dark mb-8">Panel de Administración</h1>

        <div class="grid grid-cols-12 gap-8">
            <!-- Sidebar Navigation -->
            <div class="col-span-12 md:col-span-3">
                <div class="bg-white p-6 rounded-2xl shadow-lg">
                    <h2 class="font-heading text-xl font-semibold mb-4">Menú</h2>
                    <nav class="space-y-2">
                        <?php foreach ($secciones_admin as $key => $value): ?>
                            <a href="?seccion=<?php echo $key; ?>"
                               class="block px-4 py-2 rounded-lg font-medium
                                      <?php echo $seccion === $key
                                          ? 'bg-primary text-white'
                                          : 'text-gray-600 hover:bg-gray-100'; ?>">
                                <?php echo $value; ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-span-12 md:col-span-9">
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <?php
                    // Include the corresponding admin section file
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
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
