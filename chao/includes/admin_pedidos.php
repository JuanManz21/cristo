<?php
// This file is included from admin.php, so we have access to $conn

// Handle cancel order action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_order') {
    $id_pedido_cancel = (int)$_POST['id_pedido'];
    $id_usuario_cancel = (int)$_POST['id_usuario'];
    $total_cancel = (float)$_POST['total'];

    $conn->beginTransaction();
    try {
        // Update order status to 'cancelado'
        $pedido_to_cancel = new Pedido($conn);
        $pedido_to_cancel->id_pedido = $id_pedido_cancel;
        $pedido_to_cancel->estado = 'cancelado';
        $pedido_to_cancel->updateStatus();

        // Refund the amount to the user's wallet
        $user_to_refund = new Usuario($conn);
        $user_to_refund->id_usuario = $id_usuario_cancel;
        $user_to_refund->agregarSaldo($total_cancel);

        $conn->commit();
        $mensaje = "Pedido #" . $id_pedido_cancel . " ha sido cancelado y el monto ha sido devuelto.";

    } catch (Exception $e) {
        $conn->rollBack();
        $error = "Error al cancelar el pedido: " . $e->getMessage();
    }
}

$pedido_model = new Pedido($conn);
$pedidos = $pedido_model->readAll();
?>

<h2 class="font-heading text-2xl font-bold mb-6">Últimos Pedidos</h2>

<div class="overflow-x-auto">
    <table class="min-w-full bg-white">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pedido</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php while ($row = $pedidos->fetch(PDO::FETCH_ASSOC)): extract($row); ?>
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">#<?php echo htmlspecialchars($id_pedido); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($nombre . ' ' . $apellido); ?></td>
                <td class="px-6 py-4 whitespace-nowrap"><?php echo date("d/m/Y", strtotime($fecha_pedido)); ?></td>
                <td class="px-6 py-4 whitespace-nowrap">$<?php echo number_format($total, 2); ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        <?php
                            switch ($estado) {
                                case 'entregado': echo 'bg-green-100 text-green-800'; break;
                                case 'cancelado': echo 'bg-red-100 text-red-800'; break;
                                default: echo 'bg-yellow-100 text-yellow-800'; break;
                            }
                        ?>">
                        <?php echo htmlspecialchars($estado); ?>
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <?php if ($estado === 'aprobado'): ?>
                        <form action="admin.php?seccion=pedidos" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas cancelar este pedido?');" class="inline">
                            <input type="hidden" name="action" value="cancel_order">
                            <input type="hidden" name="id_pedido" value="<?php echo $id_pedido; ?>">
                            <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">
                            <input type="hidden" name="total" value="<?php echo $total; ?>">
                            <button type="submit" class="text-red-600 hover:text-red-900">Cancelar</button>
                        </form>
                    <?php else: ?>
                        <span class="text-gray-400">N/A</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
