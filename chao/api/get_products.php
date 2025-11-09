<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../models/Producto.php';

$database = new Database();
$db = $database->getConnection();

$producto = new Producto($db);

// Get category filter if provided
$categoria_id = isset($_GET['categoria']) ? $_GET['categoria'] : null;

if($categoria_id) {
    $stmt = $producto->readByCategory($categoria_id);
} else {
    $stmt = $producto->readAll();
}

$num = $stmt->rowCount();

if($num > 0) {
    $productos_arr = array();
    $productos_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $producto_item = array(
            "id_producto" => $id_producto,
            "nombre" => $nombre,
            "descripcion" => html_entity_decode($descripcion),
            "precio" => $precio,
            "categoria_nombre" => $categoria_nombre,
            "color_hex" => $color_hex,
            "imagen_url" => $imagen_url,
            "mensaje_emocional" => html_entity_decode($mensaje_emocional),
            "disponible" => $disponible
        );

        array_push($productos_arr["records"], $producto_item);
    }

    http_response_code(200);
    echo json_encode($productos_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No se encontraron productos."));
}
?>
