<?php
// The main dashboard.php file provides $db_conn and $current_user_id
// and has already included the Pedido model.
if (!isset($db_conn) || !isset($current_user_id)) {
    die("Error: This file should not be accessed directly.");
}
$pedido_model = new Pedido($db_conn);
$pedidos = $pedido_model->readByUser($current_user_id)->fetchAll(PDO::FETCH_ASSOC);

// A map for styling order statuses
$status_styles = [
    'pendiente' => 'bg-yellow-100 text-yellow-800',
    'confirmado' => 'bg-blue-100 text-blue-800',
    'preparando' => 'bg-indigo-100 text-indigo-800',
    'entregado' => 'bg-green-100 text-green-800',
    'cancelado' => 'bg-red-100 text-red-800',
    'default' => 'bg-gray-100 text-gray-800'
];
?>

<div class="space-y-8">
    <h3 class="text-2xl font-heading font-semibold text-text-dark">Historial de Pedidos</h3>

    <?php if (empty($pedidos)): ?>
        <div class="text-center py-12 border-2 border-dashed rounded-xl">
            <p class="text-gray-500">Aún no has realizado ningún pedido.</p>
            <a href="productos.php" class="mt-4 inline-block bg-primary text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-800 transition-colors">
                Realizar mi primer pedido
            </a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pedido ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Acciones</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($pedidos as $p): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#<?php echo $p['id_pedido']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d M, Y', strtotime($p['fecha_pedido'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php $style = $status_styles[strtolower($p['estado'])] ?? $status_styles['default']; ?>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $style; ?>">
                                    <?php echo ucfirst($p['estado']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-text-dark">$<?php echo number_format($p['total'], 2); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="#" class="text-primary hover:text-green-700">Ver Detalles</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
