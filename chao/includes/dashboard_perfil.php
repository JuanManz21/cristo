<?php
// The main dashboard.php file provides $db_conn and $current_user_id
if (!isset($db_conn) || !isset($current_user_id)) {
    die("Error: This file should not be accessed directly.");
}

$usuario = new Usuario($db_conn);
$usuario->id_usuario = $current_user_id;
$usuario->readById();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Update logic
    $usuario->nombre = trim($_POST['nombre']);
    $usuario->apellido = trim($_POST['apellido']);
    $usuario->email = trim($_POST['email']);
    $usuario->telefono = trim($_POST['telefono']);
    $usuario->direccion = trim($_POST['direccion']);

    if ($usuario->update()) {
        $success = "Tu perfil ha sido actualizado con éxito.";
        // Also update session name in case it changed
        $_SESSION['usuario_nombre'] = $usuario->nombre;
    } else {
        $error = "Hubo un error al actualizar tu perfil.";
    }
    // Re-fetch data to display updated info
    $usuario->readById();
}
?>

<div class="space-y-8">
    <h3 class="text-2xl font-heading font-semibold text-text-dark">Mi Perfil</h3>

    <?php if ($success): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
            <p class="font-bold">Éxito</p>
            <p><?php echo htmlspecialchars($success); ?></p>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
            <p class="font-bold">Error</p>
            <p><?php echo htmlspecialchars($error); ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="dashboard.php?seccion=perfil" class="space-y-6 max-w-lg">
        <input type="hidden" name="update_profile" value="1">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nombre" class="block text-sm font-semibold text-gray-700">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($usuario->nombre); ?>" required class="mt-1 block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
            <div>
                <label for="apellido" class="block text-sm font-semibold text-gray-700">Apellido</label>
                <input type="text" name="apellido" id="apellido" value="<?php echo htmlspecialchars($usuario->apellido); ?>" required class="mt-1 block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
        </div>
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($usuario->email); ?>" required class="mt-1 block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
        </div>
        <div>
            <label for="telefono" class="block text-sm font-semibold text-gray-700">Teléfono</label>
            <input type="tel" name="telefono" id="telefono" value="<?php echo htmlspecialchars($usuario->telefono); ?>" class="mt-1 block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
        </div>
        <div>
            <label for="direccion" class="block text-sm font-semibold text-gray-700">Dirección</label>
            <textarea name="direccion" id="direccion" rows="3" class="mt-1 block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"><?php echo htmlspecialchars($usuario->direccion); ?></textarea>
        </div>
        <div>
            <button type="submit" class="w-full md:w-auto bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-800 transition-colors">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>
