<?php
$page_title = "Recibo del Pedido";
require_once 'includes/header.php';
require_once 'models/Pedido.php';

// Must be logged in
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Order ID must be provided
if (!isset($_GET['id_pedido'])) {
    header('Location: dashboard.php');
    exit();
}

$id_pedido = (int)$_GET['id_pedido'];

$pedido_model = new Pedido($conn);
$pedido_model->id_pedido = $id_pedido;
$order_data = $pedido_model->readOneWithDetails();

// Security check: ensure the order belongs to the logged-in user
if (!$order_data || $order_data['order']['id_usuario'] != $_SESSION['usuario_id']) {
    // Redirect to dashboard if order not found or doesn't belong to user
    header('Location: dashboard.php?seccion=pedidos');
    exit();
}

$pedido = $order_data['order'];
$detalles = $order_data['details'];
?>

<main class="py-16 bg-warm-bg">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white p-8 md:p-12 rounded-2xl shadow-lg">
            <div class="text-center mb-10 border-b pb-8">
                <h1 class="font-heading text-4xl font-bold text-primary">¡Gracias por tu compra!</h1>
                <p class="text-lg text-gray-600 mt-2">Tu pedido ha sido confirmado.</p>
            </div>

            <div class="mb-8">
                <h2 class="font-heading text-2xl font-semibold text-text-dark mb-4">Resumen del Pedido</h2>
                <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-gray-700">
                    <p><strong>Número de Pedido:</strong></p>
                    <p class="text-right">#<?php echo htmlspecialchars($pedido['id_pedido']); ?></p>

                    <p><strong>Fecha del Pedido:</strong></p>
                    <p class="text-right"><?php echo date("d/m/Y H:i", strtotime($pedido['fecha_pedido'])); ?></p>

                    <p><strong>Total Pagado:</strong></p>
                    <p class="text-right font-bold text-primary text-xl">$<?php echo number_format($pedido['total'], 2); ?></p>

                    <p><strong>Método de Pago:</strong></p>
                    <p class="text-right">Billetera</p>
                </div>
            </div>

            <div>
                <h3 class="font-heading text-xl font-semibold text-text-dark mb-4">Productos Comprados</h3>
                <div class="space-y-4">
                    <?php foreach ($detalles as $detalle): ?>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <img src="<?php echo htmlspecialchars($detalle['imagen_url']); ?>" alt="<?php echo htmlspecialchars($detalle['producto_nombre']); ?>" class="w-16 h-16 rounded-lg mr-4 shadow-sm">
                            <div>
                                <p class="font-semibold text-text-dark"><?php echo htmlspecialchars($detalle['producto_nombre']); ?></p>
                                <p class="text-sm text-gray-500">Cantidad: <?php echo $detalle['cantidad']; ?></p>
                            </div>
                        </div>
                        <p class="font-bold text-gray-800">$<?php echo number_format($detalle['subtotal'], 2); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="productos.php" class="inline-block bg-secondary text-white px-8 py-3 rounded-lg font-semibold hover:bg-lime-600 transition-colors mr-4">
                    Seguir Comprando
                </a>
                <a href="dashboard.php?seccion=pedidos" class="inline-block bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-800 transition-colors">
                    Ver Mis Pedidos
                </a>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
