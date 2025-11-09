<?php
$page_title = "Finalizar Compra";
require_once 'includes/header.php';
require_once 'models/Usuario.php';
require_once 'models/Pedido.php';
require_once 'models/DetallePedido.php';

// Must be logged in
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Redirect if cart is empty
if (empty($_SESSION['carrito'])) {
    header('Location: carrito.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$usuario = new Usuario($conn);
$usuario->id_usuario = $usuario_id;
$usuario->leerUno();

// Calculate total
$subtotal = 0;
$cart_items_details = [];
if (!empty($_SESSION['carrito'])) {
    $product_ids = array_keys($_SESSION['carrito']);
    $products_in_cart = $producto_model->readByIds($product_ids);

    foreach ($products_in_cart as $product) {
        $product_id = $product['id_producto'];
        $quantity = $_SESSION['carrito'][$product_id];
        $item_total = $product['precio'] * $quantity;
        $subtotal += $item_total;
        $cart_items_details[] = [
            'id_producto' => $product_id,
            'cantidad' => $quantity,
            'precio_unitario' => $product['precio']
        ];
    }
}

$total = $subtotal; // For simplicity, no taxes or shipping

// Handle checkout logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($usuario->saldo >= $total) {
        // Start transaction
        $conn->beginTransaction();

        try {
            // 1. Create Order
            $pedido = new Pedido($conn);
            $pedido->id_usuario = $usuario_id;
            $pedido->total = $total;
            $pedido->direccion_entrega = 'Dirección de prueba'; // Placeholder
            $pedido->metodo_pago = 'billetera';

            $id_pedido = $pedido->crear();

            if ($id_pedido) {
                // 2. Create Order Details
                $detalle_pedido = new DetallePedido($conn);
                $detalle_pedido->id_pedido = $id_pedido;
                foreach($cart_items_details as $item) {
                    $detalle_pedido->id_producto = $item['id_producto'];
                    $detalle_pedido->cantidad = $item['cantidad'];
                    $detalle_pedido->precio_unitario = $item['precio_unitario'];
                    $detalle_pedido->crear();
                }

                // 3. Deduct from balance
                $usuario->actualizarSaldo(-$total);

                // Commit transaction
                $conn->commit();

                // 4. Clear cart and redirect to a success page (receipt)
                unset($_SESSION['carrito']);
                header("Location: recibo.php?id_pedido=" . $id_pedido);
                exit();
            } else {
                throw new Exception("No se pudo crear el pedido.");
            }

        } catch (Exception $e) {
            $conn->rollBack();
            $error = "Error al procesar el pedido: " . $e->getMessage();
        }
    } else {
        $error = "Saldo insuficiente. Por favor, recargue su billetera.";
    }
}
?>

<main class="py-16 bg-warm-bg">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white p-8 rounded-2xl shadow-lg">
            <h1 class="font-heading text-3xl font-bold text-text-dark mb-6">Resumen del Pedido</h1>

            <?php if (isset($error)): ?>
                <div class="bg-red-200 text-red-800 p-4 rounded-lg mb-6">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="space-y-4 mb-6">
                 <?php foreach ($cart_items_details as $item):
                    // This is inefficient but ok for a summary page
                    $producto_model->id_producto = $item['id_producto'];
                    $producto_model->leerUno();
                ?>
                <div class="flex justify-between">
                    <span class="text-gray-600"><?php echo htmlspecialchars($producto_model->nombre); ?> x <?php echo $item['cantidad']; ?></span>
                    <span class="font-semibold">$<?php echo number_format($item['precio_unitario'] * $item['cantidad'], 2); ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between font-bold text-xl mb-4">
                    <span>Total a Pagar:</span>
                    <span>$<?php echo number_format($total, 2); ?></span>
                </div>
                <div class="flex justify-between text-lg text-green-700">
                    <span>Tu Saldo Actual:</span>
                    <span>$<?php echo number_format($usuario->saldo, 2); ?></span>
                </div>
            </div>

            <form action="checkout.php" method="POST" class="mt-8">
                <button type="submit" class="w-full bg-primary text-white text-center px-12 py-4 rounded-lg font-semibold hover:bg-green-800 transition-colors"
                    <?php if ($usuario->saldo < $total) echo 'disabled'; ?>>
                    <?php echo ($usuario->saldo >= $total) ? 'Confirmar y Pagar' : 'Saldo Insuficiente'; ?>
                </button>
            </form>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
