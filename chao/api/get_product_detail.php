<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../models/Producto.php';

$database = new Database();
$db = $database->getConnection();

$producto = new Producto($db);

// Get product ID from URL parameter
$product_id = isset($_GET['id']) ? $_GET['id'] : null;

if(!$product_id) {
    http_response_code(400);
    echo json_encode(array("message" => "ID de producto requerido."));
    exit();
}

$producto->id_producto = $product_id;
$product_data = $producto->readOne();

if($product_data) {
    // Get personalized messages for this category
    $query = "SELECT * FROM mensajes_personalizados
              WHERE id_categoria_emocional = :categoria_id AND activo = 1
              ORDER BY RAND() LIMIT 3";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':categoria_id', $product_data['id_categoria_emocional']);
    $stmt->execute();

    $mensajes = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mensajes[] = array(
            "titulo" => $row['titulo'],
            "contenido" => html_entity_decode($row['contenido']),
            "ocasion" => $row['ocasion']
        );
    }

    $product_response = array(
        "id_producto" => $product_data['id_producto'],
        "nombre" => $product_data['nombre'],
        "descripcion" => html_entity_decode($product_data['descripcion']),
        "precio" => $product_data['precio'],
        "categoria_nombre" => $product_data['categoria_nombre'],
        "categoria_descripcion" => $product_data['categoria_descripcion'],
        "color_hex" => $product_data['color_hex'],
        "imagen_url" => $product_data['imagen_url'],
        "mensaje_emocional" => html_entity_decode($product_data['mensaje_emocional']),
        "disponible" => $product_data['disponible'],
        "mensajes_sugeridos" => $mensajes
    );

    http_response_code(200);
    echo json_encode($product_response);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Producto no encontrado."));
}
?>
