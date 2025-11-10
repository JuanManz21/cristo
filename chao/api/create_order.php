<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Pedido.php';
include_once '../models/DetallePedido.php';
include_once '../models/Usuario.php';

$database = new Database();
$db = $database->getConnection();

// Get posted data
$data = json_decode(file_get_contents("php://input"));

if(!$data) {
    http_response_code(400);
    echo json_encode(array("message" => "Datos incompletos."));
    exit();
}

try {
    // Start transaction
    $db->beginTransaction();

    // Create or get user
    $usuario = new Usuario($db);
    $usuario->email = $data->customer->email;

    if(!$usuario->readByEmail()) {
        // Create new user
        $usuario->nombre = $data->customer->nombre;
        $usuario->apellido = $data->customer->apellido;
        $usuario->telefono = $data->customer->telefono;
        $usuario->tipo_usuario = 'cliente';
        $usuario->password_hash = $usuario->hashPassword('temp_password_' . time());

        if(!$usuario->create()) {
            throw new Exception("Error al crear usuario");
        }
    }

    // Create order
    $pedido = new Pedido($db);
    $pedido->id_usuario = $usuario->id_usuario;
    $pedido->fecha_entrega = $data->delivery->fecha_entrega;
    $pedido->direccion_entrega = $data->delivery->direccion .
                                 ($data->delivery->instrucciones ? "\n\nInstrucciones: " . $data->delivery->instrucciones : "");
    $pedido->mensaje_personalizado = $data->mensaje_global ?? '';
    $pedido->total = $data->totals->total;
    $pedido->metodo_pago = $data->payment->metodo;

    if(!$pedido->create()) {
        throw new Exception("Error al crear pedido");
    }

    // Create order details
    $detalle = new DetallePedido($db);
    foreach($data->items as $item) {
        $detalle->id_pedido = $pedido->id_pedido;
        $detalle->id_producto = $item->id;
        $detalle->cantidad = $item->quantity;
        $detalle->precio_unitario = $item->price;
        $detalle->subtotal = $item->price * $item->quantity;
        $detalle->mensaje_especial = $item->personalMessage ?? '';

        if(!$detalle->create()) {
            throw new Exception("Error al crear detalle de pedido");
        }
    }

    // Create support session if requested
    if($data->sesion_apoyo) {
        $support_query = "INSERT INTO sesiones_apoyo (id_usuario, tipo_apoyo, descripcion_situacion)
                         VALUES (:user_id, 'celebracion', 'Sesión solicitada con pedido')";
        $support_stmt = $db->prepare($support_query);
        $support_stmt->bindParam(':user_id', $usuario->id_usuario);
        $support_stmt->execute();
    }

    // Commit transaction
    $db->commit();

    // Generate order number
    $order_number = "LIVE-" . date('Y') . "-" . str_pad($pedido->id_pedido, 6, '0', STR_PAD_LEFT);

    http_response_code(201);
    echo json_encode(array(
        "message" => "Pedido creado exitosamente.",
        "order_id" => $pedido->id_pedido,
        "order_number" => $order_number,
        "total" => $data->totals->total
    ));

} catch(Exception $e) {
    // Rollback transaction
    $db->rollback();

    http_response_code(500);
    echo json_encode(array("message" => "Error al procesar pedido: " . $e->getMessage()));
}
?>
