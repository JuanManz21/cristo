<?php
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de usuario requerido']);
    exit;
}

try {
    $pdo = Database::getConnection();
    $userId = $_GET['user_id'];

    // Get order count
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM pedidos WHERE id_cliente = ?");
    $stmt->execute([$userId]);
    $orderCount = $stmt->fetch()['total'];

    // Get support session count
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM sesiones_apoyo WHERE id_cliente = ?");
    $stmt->execute([$userId]);
    $sessionCount = $stmt->fetch()['total'];

    // Calculate sweet moments (completed orders + completed sessions)
    $stmt = $pdo->prepare("
        SELECT
            (SELECT COUNT(*) FROM pedidos WHERE id_cliente = ? AND estado = 'Entregado') +
            (SELECT COUNT(*) FROM sesiones_apoyo WHERE id_cliente = ? AND estado = 'Completada') as moments
    ");
    $stmt->execute([$userId, $userId]);
    $moments = $stmt->fetch()['moments'];

    echo json_encode([
        'success' => true,
        'stats' => [
            'orders' => (int)$orderCount,
            'sessions' => (int)$sessionCount,
            'moments' => (int)$moments
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
    ]);
}
?>
