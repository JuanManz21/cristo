<?php
$page_title = "Apoyo Emocional";
require_once 'includes/header.php';
require_once 'models/SesionApoyo.php';

$success = '';
$error = '';

// Form submission logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {
    $tipo_apoyo = trim($_POST['tipo_apoyo']);
    $descripcion = trim($_POST['descripcion']);

    if (empty($tipo_apoyo) || empty($descripcion)) {
        $error = "Por favor, completa todos los campos del formulario.";
    } else {
        $sesion = new SesionApoyo($conn);
        $sesion->id_usuario = $_SESSION['usuario_id'];
        $sesion->tipo_apoyo = $tipo_apoyo;
        $sesion->descripcion_situacion = $descripcion;
        $sesion->consejo_brindado = null; // To be filled by admin
        $sesion->duracion_minutos = 0;   // To be filled by admin

        if ($sesion->create()) {
            $success = "Tu solicitud de apoyo ha sido enviada con éxito. Nuestro equipo se pondrá en contacto contigo pronto.";
        } else {
            $error = "Hubo un error al enviar tu solicitud. Por favor, intenta de nuevo.";
        }
    }
}
?>

<main class="py-16 bg-warm-bg">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="font-heading text-4xl font-bold text-text-dark mb-4">Apoyo Emocional Personalizado</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">En LIVE, creemos que el bienestar va más allá del sabor. Ofrecemos un espacio seguro y confidencial para que puedas compartir tus emociones y recibir orientación de nuestro equipo de apoyo.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12">
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <!-- Logged In View: Show Form -->
                <h2 class="font-heading text-3xl text-text-dark mb-6">Solicita una Sesión de Apoyo</h2>

                <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                        <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
                    </div>
                <?php endif; ?>
                 <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                        <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!$success): // Only show form if not successfully submitted ?>
                <form action="apoyo-emocional.php" method="POST" class="space-y-6">
                    <div>
                        <label for="tipo_apoyo" class="block text-sm font-semibold text-text-dark mb-2">¿Sobre qué te gustaría conversar?</label>
                        <select name="tipo_apoyo" id="tipo_apoyo" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:outline-none transition">
                            <option value="">Selecciona un motivo...</option>
                            <option value="celebracion">Celebrar un logro</option>
                            <option value="estres">Manejo del estrés</option>
                            <option value="tristeza">Momentos de tristeza o duelo</option>
                            <option value="ansiedad">Ansiedad o preocupaciones</option>
                            <option value="motivacion">Buscar motivación o claridad</option>
                            <option value="otro">Otro motivo</option>
                        </select>
                    </div>
                    <div>
                        <label for="descripcion" class="block text-sm font-semibold text-text-dark mb-2">Cuéntanos un poco sobre tu situación (confidencial)</label>
                        <textarea name="descripcion" id="descripcion" rows="6" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:outline-none transition" placeholder="Tu mensaje aquí..."></textarea>
                    </div>
                    <div>
                        <button type="submit" class="w-full md:w-auto bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-800 transition-colors">
                            Enviar Solicitud
                        </button>
                    </div>
                </form>
                <?php endif; ?>

            <?php else: ?>
                <!-- Logged Out View -->
                <div class="text-center">
                    <h2 class="font-heading text-3xl text-text-dark mb-4">Inicia Sesión para Acceder</h2>
                    <p class="text-gray-600 mb-8">Para solicitar una sesión de apoyo confidencial, por favor inicia sesión o crea una cuenta.</p>
                    <div class="flex justify-center gap-4">
                        <a href="login.php" class="inline-block bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-800 transition-colors">
                            Iniciar Sesión
                        </a>
                        <a href="registro.php" class="inline-block bg-secondary text-white px-8 py-3 rounded-lg font-semibold hover:bg-lime-600 transition-colors">
                            Crear Cuenta
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>
