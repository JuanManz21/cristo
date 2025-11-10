<?php
class Producto {
    private $conn;
    private $table_name = "productos";

    public $id_producto;
    public $nombre;
    public $descripcion;
    public $precio;
    public $id_categoria_emocional;
    public $imagen_url;
    public $mensaje_emocional;
    public $disponible;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerCategorias() {
        $query = "SELECT id_categoria as id, nombre, descripcion FROM categorias_emocionales ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProductosDestacados($limit) {
        $query = "SELECT
                    id_producto,
                    nombre,
                    descripcion,
                    precio,
                    imagen_url,
                    mensaje_emocional
                  FROM " . $this->table_name . "
                  WHERE disponible = 1
                  ORDER BY RAND()
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Read all available products
    public function readAll() {
        $query = "SELECT p.*, ce.nombre as categoria_nombre, ce.color_hex
                  FROM " . $this->table_name . " p
                  LEFT JOIN categorias_emocionales ce ON p.id_categoria_emocional = ce.id_categoria
                  WHERE p.disponible = 1
                  ORDER BY p.nombre";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single product
    public function readOne() {
        $query = "SELECT p.*, ce.nombre as categoria_nombre, ce.color_hex, ce.descripcion as categoria_descripcion
                  FROM " . $this->table_name . " p
                  LEFT JOIN categorias_emocionales ce ON p.id_categoria_emocional = ce.id_categoria
                  WHERE p.id_producto = :id_producto
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_producto', $this->id_producto);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nombre = $row['nombre'];
            $this->descripcion = $row['descripcion'];
            $this->precio = $row['precio'];
            $this->id_categoria_emocional = $row['id_categoria_emocional'];
            $this->imagen_url = $row['imagen_url'];
            $this->mensaje_emocional = $row['mensaje_emocional'];
            $this->disponible = $row['disponible'];
            return $row;
        }
        return false;
    }

    // Get products by emotional category
    public function readByCategory($categoria_id) {
        $query = "SELECT p.*, ce.nombre as categoria_nombre, ce.color_hex
                  FROM " . $this->table_name . " p
                  LEFT JOIN categorias_emocionales ce ON p.id_categoria_emocional = ce.id_categoria
                  WHERE p.id_categoria_emocional = :categoria_id AND p.disponible = 1
                  ORDER BY p.nombre";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':categoria_id', $categoria_id);
        $stmt->execute();
        return $stmt;
    }

    public function buscarProductos($busqueda = '', $categoria_id = null, $orden = 'nombre') {
        $query = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.imagen_url, p.mensaje_emocional, c.nombre as categoria_nombre
                  FROM " . $this->table_name . " p
                  LEFT JOIN categorias_emocionales c ON p.id_categoria_emocional = c.id_categoria
                  WHERE p.disponible = 1";

        $params = [];

        if (!empty($busqueda)) {
            $query .= " AND (p.nombre LIKE :busqueda OR p.descripcion LIKE :busqueda)";
            $params[':busqueda'] = '%' . $busqueda . '%';
        }

        if (!empty($categoria_id)) {
            $query .= " AND p.id_categoria_emocional = :categoria_id";
            $params[':categoria_id'] = $categoria_id;
        }

        $order_clause = " ORDER BY ";
        switch ($orden) {
            case 'precio_asc':
                $order_clause .= "p.precio ASC";
                break;
            case 'precio_desc':
                $order_clause .= "p.precio DESC";
                break;
            default:
                $order_clause .= "p.nombre ASC";
        }
        $query .= $order_clause;

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readByIds($ids) {
        if (empty($ids)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $query = "SELECT id_producto, nombre, precio, imagen_url FROM " . $this->table_name . " WHERE id_producto IN (" . $placeholders . ")";

        $stmt = $this->conn->prepare($query);

        // PDO cannot bind an array to a single placeholder, so we must bind each value.
        // However, execute() can take an array of values which works correctly with IN() clauses.
        $stmt->execute(array_values($ids));

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
