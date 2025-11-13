<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/SesionApoyo.php';
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
    $usuario->email = $data->email;

    if(!$usuario->readByEmail()) {
        // Create new user
        $usuario->nombre = $data->nombre;
        $usuario->apellido = ''; // We only have full name
        $usuario->telefono = $data->telefono;
        $usuario->tipo_usuario = 'cliente';
        $usuario->password_hash = $usuario->hashPassword('temp_password_' . time());

        if(!$usuario->create()) {
            throw new Exception("Error al crear usuario");
        }
    }

    // Create support session request
    $sesion = new SesionApoyo($db);
    $sesion->id_usuario = $usuario->id_usuario;
    $sesion->tipo_apoyo = $data->tipo_apoyo;
    $sesion->descripcion_situacion = $data->descripcion_situacion;
    $sesion->duracion_minutos = 15; // Default session duration

    if(!$sesion->create()) {
        throw new Exception("Error al crear sesión de apoyo");
    }

    // Store additional request details in a separate table (for scheduling)
    $request_query = "INSERT INTO solicitudes_apoyo
                     (id_sesion, urgencia, horarios_preferidos, edad, fecha_solicitud)
                     VALUES (:id_sesion, :urgencia, :horarios_preferidos, :edad, NOW())";

    $request_stmt = $db->prepare($request_query);
    $request_stmt->bindParam(':id_sesion', $sesion->id_sesion);
    $request_stmt->bindParam(':urgencia', $data->urgencia);
    $request_stmt->bindParam(':horarios_preferidos', json_encode($data->horarios_preferidos));
    $request_stmt->bindParam(':edad', $data->edad);

    if(!$request_stmt->execute()) {
        throw new Exception("Error al guardar detalles de solicitud");
    }

    // Commit transaction
    $db->commit();

    // Generate request number
    $request_number = "APOYO-" . date('Y') . "-" . str_pad($sesion->id_sesion, 6, '0', STR_PAD_LEFT);

    http_response_code(201);
    echo json_encode(array(
        "message" => "Solicitud de apoyo creada exitosamente.",
        "session_id" => $sesion->id_sesion,
        "request_number" => $request_number,
        "estimated_contact" => "24 horas"
    ));

} catch(Exception $e) {
    // Rollback transaction
    $db->rollback();

    http_response_code(500);
    echo json_encode(array("message" => "Error al procesar solicitud: " . $e->getMessage()));
}
?>
