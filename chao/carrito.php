<?php
$page_title = "Mi Carrito de Compras";
require_once 'includes/header.php';

// Handle cart actions (update, remove)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $product_id = (int)$_POST['producto_id'];

    if ($_POST['action'] === 'update' && isset($_POST['cantidad'])) {
        $quantity = (int)$_POST['cantidad'];
        if ($quantity > 0) {
            $_SESSION['carrito'][$product_id] = $quantity;
        } else {
            // Remove if quantity is 0 or less
            unset($_SESSION['carrito'][$product_id]);
        }
    } elseif ($_POST['action'] === 'remove') {
        unset($_SESSION['carrito'][$product_id]);
    }
    // Redirect to the same page to prevent form resubmission
    header('Location: carrito.php');
    exit();
}

// Fetch product details for items in cart
$cart_items = [];
$subtotal = 0;
if (!empty($_SESSION['carrito'])) {
    $product_ids = array_keys($_SESSION['carrito']);
    // The product model is already instantiated in header.php as $producto_model
    $products_in_cart = $producto_model->readByIds($product_ids);

    foreach ($products_in_cart as $product) {
        $product_id = $product['id_producto'];
        $quantity = $_SESSION['carrito'][$product_id];
        $item_total = $product['precio'] * $quantity;
        $subtotal += $item_total;

        $cart_items[] = [
            'id' => $product_id,
            'nombre' => $product['nombre'],
            'precio' => $product['precio'],
            'imagen' => $product['imagen_url'],
            'cantidad' => $quantity,
            'total_item' => $item_total
        ];
    }
}
?>

<main class="py-16 bg-warm-bg">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="font-heading text-4xl font-bold text-text-dark mb-4">Mi Carrito</h1>
        </div>

        <?php if (empty($cart_items)): ?>
            <div class="bg-white p-12 rounded-2xl shadow-lg text-center">
                <h2 class="font-heading text-2xl text-text-dark mb-4">Tu carrito está vacío</h2>
                <p class="text-gray-600 mb-8">Parece que no has agregado ningún producto todavía.</p>
                <a href="productos.php" class="inline-block bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-800 transition-colors">
                    Explorar Productos
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white p-8 rounded-2xl shadow-lg">
                <!-- Cart Items -->
                <div class="space-y-6">
                    <?php foreach ($cart_items as $item): ?>
                    <div class="grid grid-cols-12 gap-4 items-center border-b pb-6">
                        <div class="col-span-3 lg:col-span-2">
                            <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>" class="rounded-lg shadow-md">
                        </div>
                        <div class="col-span-9 lg:col-span-5">
                            <h3 class="font-heading text-lg font-semibold text-text-dark"><?php echo htmlspecialchars($item['nombre']); ?></h3>
                            <p class="text-primary font-bold">$<?php echo number_format($item['precio'], 2); ?></p>
                        </div>
                        <div class="col-span-6 lg:col-span-3">
                            <form action="carrito.php" method="POST" class="flex items-center">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="cantidad" value="<?php echo $item['cantidad']; ?>" min="1" class="w-20 px-3 py-2 rounded-lg border border-gray-300 focus:border-primary focus:outline-none">
                                <button type="submit" class="ml-2 px-3 py-2 bg-secondary text-white rounded-lg hover:bg-lime-600 text-sm">Actualizar</button>
                            </form>
                        </div>
                        <div class="col-span-4 lg:col-span-1 text-right">
                            <p class="font-bold text-lg">$<?php echo number_format($item['total_item'], 2); ?></p>
                        </div>
                        <div class="col-span-2 lg:col-span-1 text-right">
                             <form action="carrito.php" method="POST">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">&times;</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Cart Summary -->
                <div class="mt-8 pt-6 border-t">
                    <div class="flex justify-end items-center">
                        <span class="text-xl font-heading text-text-dark">Subtotal:</span>
                        <span class="text-2xl font-bold text-primary ml-4">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="flex justify-end mt-6">
                        <a href="checkout.php" class="w-full md:w-auto bg-primary text-white text-center px-12 py-4 rounded-lg font-semibold hover:bg-green-800 transition-colors">
                            Proceder al Pago
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>
