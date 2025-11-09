<?php
// Included from admin.php, $conn is available
$usuario_model = new Usuario($conn);
$usuarios = $usuario_model->readAll(); // We need to create this method

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $user_id_action = (int)$_POST['id_usuario'];

    if ($_POST['action'] === 'delete') {
        $usuario_model->id_usuario = $user_id_action;
        if ($usuario_model->delete()) {
            $mensaje = "Usuario eliminado con éxito.";
        } else {
            $error = "No se pudo eliminar el usuario.";
        }
    }

    if ($_POST['action'] === 'refund' && isset($_POST['monto_devolucion'])) {
        $monto = (float)$_POST['monto_devolucion'];
        $usuario_model->id_usuario = $user_id_action;
        if($usuario_model->agregarSaldo($monto)) {
            $mensaje = "Devolución realizada con éxito.";
        } else {
            $error = "Error al procesar la devolución.";
        }
    }
    // Refresh user list
    $usuarios = $usuario_model->readAll();
}
?>

<h2 class="font-heading text-2xl font-bold mb-6">Gestionar Usuarios</h2>

<?php if (isset($mensaje)): ?>
<div class="bg-green-100 text-green-800 p-4 rounded-lg mb-6"><?php echo $mensaje; ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
<div class="bg-red-100 text-red-800 p-4 rounded-lg mb-6"><?php echo $error; ?></div>
<?php endif; ?>

<div class="overflow-x-auto">
    <table class="min-w-full bg-white">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saldo</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php while ($row = $usuarios->fetch(PDO::FETCH_ASSOC)): extract($row); ?>
            <tr>
                <td class="px-6 py-4"><?php echo htmlspecialchars($id_usuario); ?></td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($email); ?></td>
                <td class="px-6 py-4">$<?php echo number_format($saldo, 2); ?></td>
                <td class="px-6 py-4 text-right">
                    <!-- Delete Form -->
                    <form action="admin.php?seccion=usuarios" method="POST" onsubmit="return confirm('¿Estás seguro?');" class="inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">
                        <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                    </form>
                    |
                    <!-- Refund Form -->
                    <form action="admin.php?seccion=usuarios" method="POST" class="inline">
                         <input type="hidden" name="action" value="refund">
                         <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">
                         <input type="number" name="monto_devolucion" step="0.01" placeholder="Monto" class="w-24 text-sm border-gray-300 rounded">
                         <button type="submit" class="text-blue-600 hover:text-blue-900">Devolución</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
