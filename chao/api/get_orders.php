<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../models/Pedido.php';

$database = new Database();
$db = $database->getConnection();

$pedido = new Pedido($db);

// Get user ID from query parameter (in real app, this would come from session)
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if($user_id) {
    // Get orders for specific user
    $stmt = $pedido->readByUser($user_id);
} else {
    // Get all orders (admin view)
    $stmt = $pedido->readAll();
}

$num = $stmt->rowCount();

if($num > 0) {
    $orders_arr = array();
    $orders_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $order_item = array(
            "id_pedido" => $id_pedido,
            "fecha_pedido" => $fecha_pedido,
            "fecha_entrega" => $fecha_entrega,
            "estado" => $estado,
            "total" => floatval($total),
            "metodo_pago" => $metodo_pago,
            "total_items" => intval($total_items),
            "cliente_nombre" => isset($nombre) ? $nombre . ' ' . $apellido : null,
            "cliente_email" => isset($email) ? $email : null
        );

        array_push($orders_arr["records"], $order_item);
    }

    http_response_code(200);
    echo json_encode($orders_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No se encontraron pedidos."));
}
?>
