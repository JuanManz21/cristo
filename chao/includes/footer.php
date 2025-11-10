<footer class="bg-text-dark text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h4 class="font-heading text-2xl font-bold mb-4">LIVE</h4>
                    <p class="text-gray-300 mb-4">
                        Postres con propósito que nutren el alma y brindan apoyo emocional.
                    </p>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Productos</h5>
                    <ul class="space-y-2 text-gray-300">
                         <?php
                         if (isset($categorias_header)) {
                             foreach ($categorias_header as $categoria) {
                                 echo '<li><a href="productos.php?categoria_id=' . $categoria['id'] . '" class="hover:text-white transition-colors">' . htmlspecialchars($categoria['nombre']) . '</a></li>';
                             }
                         }
                         ?>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Servicios</h5>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="apoyo-emocional.php" class="hover:text-white transition-colors">Apoyo Emocional</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-semibold mb-4">Contacto</h5>
                    <ul class="space-y-2 text-gray-300">
                        <li>Av. Principal 123, Ciudad</li>
                        <li>+1 (555) 123-4567</li>
                        <li>hola@live-postres.com</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-600 mt-8 pt-8 text-center text-gray-300">
                <p>&copy; <?php echo date("Y"); ?> LIVE - Postres con Propósito. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>
