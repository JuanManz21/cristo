<?php
require_once '../config/database.php';
require_once '../models/SesionApoyo.php';

header('Content-Type: application/json');

if (!isset($_GET['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de usuario requerido']);
    exit;
}

try {
    $sesionApoyo = new SesionApoyo();
    $sessions = $sesionApoyo->getByUserId($_GET['user_id']);

    // Format sessions for frontend
    $formattedSessions = array_map(function($session) {
        return [
            'id' => $session['id'],
            'type' => $session['tipo_apoyo'],
            'date' => date('d/m/Y H:i', strtotime($session['fecha_solicitud'])),
            'status' => $session['estado'],
            'description' => $session['descripcion_situacion'],
            'advice' => $session['consejo_recibido'],
            'duration' => $session['duracion_minutos'] ?? 30,
            'rating' => $session['calificacion']
        ];
    }, $sessions);

    echo json_encode([
        'success' => true,
        'sessions' => $formattedSessions
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener sesiones: ' . $e->getMessage()
    ]);
}
?>
