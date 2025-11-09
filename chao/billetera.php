<?php
$page_title = "Mi Billetera";
require_once 'includes/header.php';
require_once 'models/Usuario.php';

// Must be logged in to view this page
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$usuario = new Usuario($conn);
$usuario->id_usuario = $usuario_id;
$usuario->leerUno();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['monto'])) {
    $monto = filter_input(INPUT_POST, 'monto', FILTER_VALIDATE_FLOAT);
    if ($monto && $monto > 0) {
        if ($usuario->agregarSaldo($monto)) {
            $mensaje = "¡Saldo agregado con éxito!";
            $usuario->leerUno(); // Refresh user data
        } else {
            $error = "No se pudo agregar el saldo. Por favor, intente de nuevo.";
        }
    } else {
        $error = "Por favor, ingrese un monto válido.";
    }
}
?>

<main class="py-16 bg-warm-bg">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-8 rounded-2xl shadow-lg">
            <h1 class="font-heading text-3xl font-bold text-text-dark mb-4">Mi Billetera</h1>
            <p class="text-xl text-gray-600 mb-6">Administra tu saldo disponible.</p>

            <div class="bg-green-100 p-6 rounded-xl mb-8">
                <p class="text-lg text-gray-700">Saldo Actual:</p>
                <p class="text-4xl font-bold text-primary">$<?php echo number_format($usuario->saldo, 2); ?></p>
            </div>

            <?php if (isset($mensaje)): ?>
                <div class="bg-green-200 text-green-800 p-4 rounded-lg mb-6">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="bg-red-200 text-red-800 p-4 rounded-lg mb-6">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="billetera.php" method="POST">
                <h2 class="text-2xl font-bold text-text-dark mb-4">Recargar Saldo</h2>
                <div class="mb-4">
                    <label for="monto" class="block text-gray-700 mb-2">Monto a Recargar</label>
                    <input type="number" id="monto" name="monto" step="0.01" min="1" class="w-full px-4 py-2 border rounded-lg" placeholder="Ej: 50.00" required>
                </div>
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-green-800 transition-colors">Agregar Saldo</button>
            </form>
        </div>
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>
