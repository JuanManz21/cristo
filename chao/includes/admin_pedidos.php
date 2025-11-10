<?php
// This file is included in admin.php
// The admin check is already done in admin.php
// The Database connection $conn is also available from admin.php

require_once 'models/Pedido.php';

$pedido_model = new Pedido($conn);
$pedidos = $pedido_model->readAll();
?>

<div class="overflow-x-auto">
    <table class="min-w-full bg-white">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">ID Pedido</th>
                <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">Cliente</th>
                <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">Fecha</th>
                <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">Total</th>
                <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">Estado</th>
                <th class="w-1/6 py-3 px-4 uppercase font-semibold text-sm">Acciones</th>
            </tr>
        </thead>
        <tbody class="text-gray-700">
            <?php while ($row = $pedidos->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td class="w-1/6 py-3 px-4"><?php echo htmlspecialchars($row['id_pedido']); ?></td>
                    <td class="w-1/6 py-3 px-4"><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></td>
                    <td class="w-1/6 py-3 px-4"><?php echo htmlspecialchars($row['fecha_pedido']); ?></td>
                    <td class="w-1/6 py-3 px-4">$<?php echo htmlspecialchars(number_format($row['total'], 2)); ?></td>
                    <td class="w-1/6 py-3 px-4"><?php echo htmlspecialchars($row['estado']); ?></td>
                    <td class="w-1/6 py-3 px-4">
                        <?php if ($row['estado'] === 'confirmado' || $row['estado'] === 'aprobado'): ?>
                            <form action="cancelar_pedido.php" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres cancelar este pedido?');">
                                <input type="hidden" name="id_pedido" value="<?php echo $row['id_pedido']; ?>">
                                <input type="hidden" name="id_usuario" value="<?php echo $row['id_usuario']; ?>">
                                <input type="hidden" name="total" value="<?php echo $row['total']; ?>">
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">Cancelar</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
