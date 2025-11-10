<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/database.php';
include_once '../models/Producto.php';

$database = new Database();
$db = $database->getConnection();

$producto = new Producto($db);

// Get search parameters
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$categoria_id = isset($_GET['categoria']) ? $_GET['categoria'] : null;
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'nombre';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Build the query
$query = "SELECT p.*, ce.nombre as categoria_nombre, ce.color_hex, ce.descripcion as categoria_descripcion
          FROM productos p
          LEFT JOIN categorias_emocionales ce ON p.id_categoria_emocional = ce.id_categoria
          WHERE p.disponible = 1";

$params = array();

// Add search filter
if (!empty($search_term)) {
    $query .= " AND (p.nombre LIKE :search OR p.descripcion LIKE :search OR ce.nombre LIKE :search)";
    $params[':search'] = '%' . $search_term . '%';
}

// Add category filter
if ($categoria_id && $categoria_id !== 'all') {
    $query .= " AND p.id_categoria_emocional = :categoria_id";
    $params[':categoria_id'] = $categoria_id;
}

// Add sorting
$allowed_sorts = ['nombre', 'precio', 'categoria_nombre'];
$allowed_orders = ['ASC', 'DESC'];

if (in_array($sort_by, $allowed_sorts) && in_array($order, $allowed_orders)) {
    if ($sort_by === 'categoria_nombre') {
        $query .= " ORDER BY ce.nombre " . $order;
    } else {
        $query .= " ORDER BY p." . $sort_by . " " . $order;
    }
} else {
    $query .= " ORDER BY p.nombre ASC";
}

$stmt = $db->prepare($query);

// Bind parameters
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->execute();
$num = $stmt->rowCount();

if($num > 0) {
    $productos_arr = array();
    $productos_arr["records"] = array();
    $productos_arr["total"] = $num;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $producto_item = array(
            "id_producto" => $id_producto,
            "nombre" => $nombre,
            "descripcion" => html_entity_decode($descripcion),
            "precio" => floatval($precio),
            "categoria_nombre" => $categoria_nombre,
            "categoria_descripcion" => html_entity_decode($categoria_descripcion),
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
    echo json_encode(array(
        "message" => "No se encontraron productos.",
        "total" => 0,
        "records" => array()
    ));
}
?>
