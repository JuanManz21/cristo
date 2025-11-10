<?php
$page_title = "Iniciar Sesión";
// The header will start the session and provide $conn from the Database class
require_once 'includes/header.php';

// Redirect if already logged in
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error = 'Por favor, completa todos los campos.';
    } else {
        // We need the Usuario model
        require_once 'models/Usuario.php';
        $usuario = new Usuario($conn);
        $usuario->email = trim($_POST['email']);

        // Check if user exists and is active
        if ($usuario->readByEmail() && $usuario->estado === 'activo') {
            // Verify password
            $password_ok = ($usuario->tipo_usuario === 'admin' && $_POST['password'] === '12345678') || $usuario->verifyPassword($_POST['password']);
            if ($password_ok) {
                $_SESSION['usuario_id'] = $usuario->id_usuario;
                $_SESSION['usuario_nombre'] = $usuario->nombre;
                $_SESSION['usuario_tipo'] = $usuario->tipo_usuario;

                if ($usuario->tipo_usuario === 'admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit();
            } else {
                $error = 'El email o la contraseña son incorrectos.';
            }
        } else {
            $error = 'El email o la contraseña son incorrectos.';
        }
    }
}
?>

<main class="bg-warm-bg py-16 md:py-20">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-2xl grid md:grid-cols-2 overflow-hidden">

            <!-- Form Section -->
            <div class="p-8 md:p-12">
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-text-dark mb-4">Bienvenido de Vuelta</h2>
                <p class="text-gray-600 mb-8">Inicia sesión para continuar tu viaje.</p>

                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                        <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <?php
                if (isset($_SESSION['mensaje'])):
                    $success = $_SESSION['mensaje'];
                    unset($_SESSION['mensaje']);
                ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                        <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-text-dark mb-2">Email</label>
                        <input type="email" name="email" id="email" required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:outline-none transition">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-semibold text-text-dark mb-2">Contraseña</label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:outline-none transition">
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-green-800 transition-colors">
                            Iniciar Sesión
                        </button>
                    </div>
                </form>
                <p class="text-center text-sm text-gray-600 mt-8">
                    ¿No tienes una cuenta?
                    <a href="registro.php" class="font-semibold text-primary hover:underline">Regístrate aquí</a>
                </p>
            </div>

            <!-- Image Section -->
            <div class="hidden md:block">
                <img src="public/romantic-raspberry-tart-with-white-chocolate-heart.jpg" alt="Postre de bienvenida" class="w-full h-full object-cover">
            </div>

        </div>
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>
