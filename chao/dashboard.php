<?php
$page_title = "Mi Cuenta";
require_once 'includes/header.php';
require_once 'models/Usuario.php';
require_once 'models/Pedido.php';
require_once 'models/SesionApoyo.php';

// Must be logged in to view this page
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Get user stats using the new model method
$usuario = new Usuario($conn);
$usuario->id_usuario = $usuario_id;
$stats = $usuario->obtenerEstadisticas();

// Define available sections and the current one
$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'pedidos';
$secciones = [
    'pedidos' => 'Mis Pedidos',
    'apoyo' => 'Mis Sesiones de Apoyo',
    'perfil' => 'Mi Perfil'
];
?>

<main class="py-16 bg-warm-bg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-left mb-12">
            <h1 class="font-heading text-4xl font-bold text-text-dark mb-2">Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></h1>
            <p class="text-xl text-gray-600">Aquí puedes gestionar tu cuenta, pedidos y sesiones de apoyo.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center space-x-4">
                <div class="bg-green-100 p-4 rounded-full">
                    <span class="text-3xl">📦</span>
                </div>
                <div>
                    <p class="text-3xl font-bold text-primary"><?php echo $stats['pedidos']['total_pedidos']; ?></p>
                    <p class="text-gray-500">Pedidos Realizados</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center space-x-4">
                <div class="bg-yellow-100 p-4 rounded-full">
                     <span class="text-3xl">💰</span>
                </div>
                <div>
                    <p class="text-3xl font-bold text-primary">$<?php echo number_format($stats['pedidos']['total_gastado'], 2); ?></p>
                    <p class="text-gray-500">Total Gastado</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center space-x-4">
                 <div class="bg-blue-100 p-4 rounded-full">
                    <span class="text-3xl">💚</span>
                </div>
                <div>
                    <p class="text-3xl font-bold text-primary"><?php echo $stats['sesiones']['total_sesiones']; ?></p>
                    <p class="text-gray-500">Sesiones de Apoyo</p>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="bg-white rounded-2xl shadow-2xl">
            <!-- Tabs Navigation -->
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
                // Include the corresponding section file
                $include_file = 'includes/dashboard_' . $seccion . '.php';
                if (file_exists($include_file)) {
                    // Pass the connection and user id to the included file
                    $db_conn = $conn;
                    $current_user_id = $usuario_id;
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
