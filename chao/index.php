<?php
$page_title = "Página de Inicio - Postres con Propósito";
require_once 'includes/header.php';

// Data fetching specific to this page
$productos_destacados = $producto_model->obtenerProductosDestacados(3);
$categorias = $categorias_header; // Use categories from header

// A simple map for category styles, matching the original HTML design
$category_styles = [
    'Celebración' => ['icon' => '🎉', 'bg' => 'from-yellow-50 to-yellow-100', 'icon-bg' => 'bg-yellow-200'],
    'Consuelo' => ['icon' => '💙', 'bg' => 'from-blue-50 to-blue-100', 'icon-bg' => 'bg-blue-200'],
    'Motivación' => ['icon' => '⭐', 'bg' => 'from-orange-50 to-orange-100', 'icon-bg' => 'bg-orange-200'],
    'Amor' => ['icon' => '💕', 'bg' => 'from-pink-50 to-pink-100', 'icon-bg' => 'bg-pink-200'],
    'Gratitud' => ['icon' => '🙏', 'bg' => 'from-green-50 to-green-100', 'icon-bg' => 'bg-green-200'],
    'default' => ['icon' => '😊', 'bg' => 'from-gray-50 to-gray-100', 'icon-bg' => 'bg-gray-200'],
];
?>

    <!-- Hero Section -->
    <section id="inicio" class="relative bg-gradient-to-br from-warm-bg to-green-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="font-heading text-5xl font-bold text-text-dark mb-6 leading-tight">
                        Dulces que <span class="text-primary">Nutren el Alma</span>
                    </h2>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        En LIVE, cada postre cuenta una historia. Combinamos la excelencia culinaria con mensajes personalizados y apoyo emocional para crear experiencias que van más allá del sabor.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="productos.php" class="bg-primary text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-green-800 transition-colors shadow-lg">
                            Explorar Productos
                        </a>
                        <a href="apoyo-emocional.php" class="bg-white text-primary border-2 border-primary px-8 py-4 rounded-lg text-lg font-semibold hover:bg-primary hover:text-white transition-colors">
                            Apoyo Emocional
                        </a>
                    </div>
                </div>
                <div class="relative">
                    <img src="public/golden-celebration-cake-with-vanilla-layers.jpg"
                         alt="Postre artesanal con mensaje personalizado"
                         class="rounded-2xl shadow-2xl w-full h-auto object-cover">
                    <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-xl shadow-lg">
                        <p class="text-sm text-gray-600 mb-2">Mensaje del día</p>
                        <p class="font-semibold text-primary">"Cada momento especial merece ser celebrado con dulzura"</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Emotional Categories -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h3 class="font-heading text-4xl font-bold text-text-dark mb-4">
                    Postres para Cada Momento Emocional
                </h3>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Nuestros productos están diseñados para acompañarte en diferentes momentos de tu vida,
                    brindando no solo sabor, sino también conexión emocional.
                </p>
            </div>
            <div class="grid md:grid-cols-3 lg:grid-cols-5 gap-6">
                <?php foreach ($categorias as $categoria):
                    $style = $category_styles[$categoria['nombre']] ?? $category_styles['default'];
                ?>
                <a href="productos.php?categoria_id=<?php echo $categoria['id']; ?>" class="bg-gradient-to-br <?php echo $style['bg']; ?> p-6 rounded-xl text-center hover:shadow-lg transition-shadow cursor-pointer">
                    <div class="w-16 h-16 <?php echo $style['icon-bg']; ?> rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl"><?php echo $style['icon']; ?></span>
                    </div>
                    <h4 class="font-semibold text-text-dark mb-2"><?php echo htmlspecialchars($categoria['nombre']); ?></h4>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($categoria['descripcion']); ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section id="productos" class="py-16 bg-warm-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h3 class="font-heading text-4xl font-bold text-text-dark mb-4">
                    Productos Destacados
                </h3>
                <p class="text-xl text-gray-600">
                    Cada postre viene con un mensaje personalizado diseñado para tocar el corazón
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($productos_destacados as $p): ?>
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow flex flex-col">
                    <a href="productos.php?id=<?php echo $p['id_producto']; ?>">
                        <img src="<?php echo htmlspecialchars($p['imagen_url']); ?>"
                             alt="<?php echo htmlspecialchars($p['nombre']); ?>"
                             class="w-full h-64 object-cover">
                    </a>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex items-center justify-between mb-2">
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
            </div>
        </div>
    </section>

    <!-- Psychological Support Section -->
    <section id="apoyo" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="font-heading text-4xl font-bold text-text-dark mb-6">
                        Apoyo Emocional Personalizado
                    </h3>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Más que postres, ofrecemos una experiencia integral de bienestar. Nuestro equipo de apoyo
                        emocional está aquí para acompañarte en momentos clave de tu vida.
                    </p>
                    <a href="apoyo-emocional.php" class="mt-8 inline-block bg-secondary text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-lime-600 transition-colors">
                        Solicitar Apoyo
                    </a>
                </div>
                <div class="relative">
                    <img src="public/comforting-chocolate-cupcakes-with-cream-frosting.jpg"
                         alt="Sesión de apoyo emocional"
                         class="rounded-2xl shadow-2xl">
                </div>
            </div>
        </div>
    </section>

<?php
require_once 'includes/footer.php';
?>
