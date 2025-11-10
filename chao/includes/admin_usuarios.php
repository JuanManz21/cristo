<?php
// This file is included in admin.php
// The admin check is already done in admin.php
// The Database connection $conn is also available from admin.php

require_once 'models/Usuario.php';

$usuario_model = new Usuario($conn);
$usuarios = $usuario_model->readAll();
?>

<div class="overflow-x-auto">
    <table class="min-w-full bg-white">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">ID Usuario</th>
                <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">Nombre</th>
                <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">Email</th>
                <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">Tipo</th>
                <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">Estado</th>
                <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php while ($row = $usuarios->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td class="w-1/6 py-3 px-4"><?php echo htmlspecialchars($row['id_usuario']); ?></td>
                    <td class="w-1/6 py-3 px-4"><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></td>
                    <td class="w-1/6 py-3 px-4"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td class="w-1/6 py-3 px-4"><?php echo htmlspecialchars($row['tipo_usuario']); ?></td>
                    <td class="w-1/6 py-3 px-4"><?php echo htmlspecialchars($row['estado']); ?></td>
                    <td class="w-1/6 py-3 px-4">
                        <?php if ($row['estado'] === 'activo'): ?>
                            <form action="desactivar_usuario.php" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres desactivar este usuario?');">
                                <input type="hidden" name="id_usuario" value="<?php echo $row['id_usuario']; ?>">
                                <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">Desactivar</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
