<?php
$page_title = "Nuestros Productos";
require_once 'includes/header.php';

// Get filter/search parameters from URL
$categoria_id = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : null;
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'nombre';

// Fetch products based on filters
$productos = $producto_model->buscarProductos($busqueda, $categoria_id, $orden);
$categorias = $categorias_header; // Use categories from header
?>

<main class="py-12 bg-warm-bg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="font-heading text-4xl font-bold text-text-dark mb-4">Nuestros Productos</h1>
            <p class="text-xl text-gray-600">Encuentra el postre perfecto para cada emoción y ocasión.</p>
        </div>

        <!-- Filters and Search Section -->
        <div class="bg-white p-6 rounded-2xl shadow-lg mb-12">
            <form method="GET" action="productos.php" class="grid md:grid-cols-4 gap-6 items-center">
                <div class="md:col-span-2">
                    <label for="buscar" class="sr-only">Buscar</label>
                    <input type="text" name="buscar" id="buscar" placeholder="Buscar por nombre o descripción..." value="<?php echo htmlspecialchars($busqueda); ?>" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:outline-none">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                     <div>
                        <label for="categoria_id" class="sr-only">Categoría</label>
                        <select name="categoria_id" id="categoria_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:outline-none">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $categoria_id == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="orden" class="sr-only">Ordenar por</label>
                        <select name="orden" id="orden" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:outline-none">
                            <option value="nombre" <?php echo $orden == 'nombre' ? 'selected' : ''; ?>>Ordenar por nombre</option>
                            <option value="precio_asc" <?php echo $orden == 'precio_asc' ? 'selected' : ''; ?>>Precio: de menor a mayor</option>
                            <option value="precio_desc" <?php echo $orden == 'precio_desc' ? 'selected' : ''; ?>>Precio: de mayor a menor</option>
                        </select>
                    </div>
                </div>
                <div class="md:col-span-4 lg:col-span-1">
                    <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-green-800 transition-colors">Aplicar Filtros</button>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($productos)): ?>
                <div class="md:col-span-2 lg:col-span-3 text-center py-12">
                    <h3 class="font-heading text-2xl text-text-dark">No se encontraron productos</h3>
                    <p class="text-gray-600 mt-2">Intenta ajustar tus filtros de búsqueda.</p>
                    <a href="productos.php" class="mt-4 inline-block bg-secondary text-white px-6 py-3 rounded-lg font-semibold hover:bg-lime-600">Ver todos los productos</a>
                </div>
            <?php else: ?>
                <?php foreach ($productos as $p): ?>
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow flex flex-col">
                    <a href="productos.php?id=<?php echo $p['id_producto']; ?>">
                        <img src="<?php echo htmlspecialchars($p['imagen_url']); ?>"
                             alt="<?php echo htmlspecialchars($p['nombre']); ?>"
                             class="w-full h-64 object-cover">
                    </a>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex items-center justify-between mb-2">
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full"><?php echo htmlspecialchars($p['categoria_nombre']); ?></span>
                            <span class="text-2xl font-bold text-primary">$<?php echo number_format($p['precio'], 2); ?></span>
                        </div>
                        <h4 class="font-heading text-xl font-semibold text-text-dark mb-2">
                            <?php echo htmlspecialchars($p['nombre']); ?>
                        </h4>
                        <p class="text-gray-600 mb-4 flex-grow">
                            <?php echo htmlspecialchars($p['descripcion']); ?>
                        </p>
                        <div class="bg-green-50 p-3 rounded-lg mb-4">
                            <p class="text-sm italic text-gray-700">
                                "<?php echo htmlspecialchars($p['mensaje_emocional']); ?>"
                            </p>
                        </div>
                        <form method="POST" action="agregar_carrito.php" class="mt-auto">
                            <input type="hidden" name="producto_id" value="<?php echo $p['id_producto']; ?>">
                            <input type="hidden" name="cantidad" value="1">
                            <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg hover:bg-green-800 transition-colors">
                                Agregar al Carrito
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>
