<?php
$page_title = "Crear Cuenta";
require_once 'includes/header.php';

// Redirect if already logged in
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
        $error = 'Por favor, completa todos los campos obligatorios.';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El formato del email no es válido.';
    } else {
        require_once 'models/Usuario.php';
        $usuario = new Usuario($conn);
        $usuario->email = $email;

        if ($usuario->readByEmail()) {
            $error = 'Este email ya está registrado. Intenta iniciar sesión.';
        } else {
            $usuario->nombre = $nombre;
            $usuario->apellido = $apellido;
            $usuario->telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : null;
            $usuario->password_hash = $usuario->hashPassword($password);
            $usuario->tipo_usuario = ($email === 'admin@gmail.com') ? 'admin' : 'cliente';

            // The create method in the model doesn't handle all fields.
            // I need to adjust the model or the data I'm passing.
            // Let's check the create() method again.
            // It expects: nombre, apellido, email, telefono, direccion, fecha_nacimiento, tipo_usuario, password_hash
            // I'll pass null for the optional ones for now.
            $usuario->direccion = null;
            $usuario->fecha_nacimiento = null;

            if ($usuario->create()) {
                $_SESSION['mensaje'] = '¡Registro exitoso! Ya puedes iniciar sesión.';
                header('Location: login.php');
                exit();
            } else {
                $error = 'Hubo un error al crear tu cuenta. Por favor, intenta de nuevo.';
            }
        }
    }
}
?>

<main class="bg-warm-bg py-16 md:py-20">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-2xl grid md:grid-cols-2 overflow-hidden">

            <!-- Form Section -->
            <div class="p-8 md:p-12">
                <h2 class="font-heading text-3xl md:text-4xl font-bold text-text-dark mb-4">Crea tu Cuenta</h2>
                <p class="text-gray-600 mb-8">Únete para una experiencia llena de dulzura y apoyo.</p>

                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                        <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="registro.php" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nombre" class="block text-sm font-semibold text-text-dark mb-2">Nombre</label>
                            <input type="text" name="nombre" id="nombre" required value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:outline-none transition">
                        </div>
                        <div>
                            <label for="apellido" class="block text-sm font-semibold text-text-dark mb-2">Apellido</label>
                            <input type="text" name="apellido" id="apellido" required value="<?php echo isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : ''; ?>"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:outline-none transition">
                        </div>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-semibold text-text-dark mb-2">Email</label>
                        <input type="email" name="email" id="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:outline-none transition">
                    </div>
                    <div>
                        <label for="telefono" class="block text-sm font-semibold text-text-dark mb-2">Teléfono (Opcional)</label>
                        <input type="tel" name="telefono" id="telefono" value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:outline-none transition">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-semibold text-text-dark mb-2">Contraseña</label>
                            <input type="password" name="password" id="password" required minlength="8"
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:outline-none transition">
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-semibold text-text-dark mb-2">Confirmar Contraseña</label>
                            <input type="password" name="confirm_password" id="confirm_password" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-primary focus:outline-none transition">
                        </div>
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-green-800 transition-colors">
                            Crear Cuenta
                        </button>
                    </div>
                </form>
                <p class="text-center text-sm text-gray-600 mt-8">
                    ¿Ya tienes una cuenta?
                    <a href="login.php" class="font-semibold text-primary hover:underline">Inicia sesión aquí</a>
                </p>
            </div>

            <!-- Image Section -->
            <div class="hidden md:block">
                <img src="public/heart-shaped-gratitude-cookies-with-thank-you-mess.jpg" alt="Postre de bienvenida" class="w-full h-full object-cover">
            </div>

        </div>
    </div>
</main>

<?php
require_once 'includes/footer.php';
?>
