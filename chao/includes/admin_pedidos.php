<?php
// This file is included from admin.php, so we have access to $conn
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
                    <a href="#" class="text-indigo-600 hover:text-indigo-900">Ver</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
