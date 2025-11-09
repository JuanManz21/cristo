<?php
class Pedido {
    private $conn;
    private $table_name = "pedidos";

    public $id_pedido;
    public $id_usuario;
    public $fecha_pedido;
    public $fecha_entrega;
    public $direccion_entrega;
    public $mensaje_personalizado;
    public $estado;
    public $total;
    public $metodo_pago;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new order
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET id_usuario=:id_usuario,
                      direccion_entrega=:direccion_entrega,
                      total=:total, metodo_pago=:metodo_pago";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        $this->fecha_entrega = htmlspecialchars(strip_tags($this->fecha_entrega));
        $this->direccion_entrega = htmlspecialchars(strip_tags($this->direccion_entrega));
        $this->total = htmlspecialchars(strip_tags($this->total));
        $this->metodo_pago = htmlspecialchars(strip_tags($this->metodo_pago));

        // Bind values
        $stmt->bindParam(":id_usuario", $this->id_usuario);
        $stmt->bindParam(":direccion_entrega", $this->direccion_entrega);
        $stmt->bindParam(":total", $this->total);
        $stmt->bindParam(":metodo_pago", $this->metodo_pago);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Read orders by user
    public function readByUser($user_id) {
        $query = "SELECT p.*, COUNT(dp.id_detalle) as total_items
                  FROM " . $this->table_name . " p
                  LEFT JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
                  WHERE p.id_usuario = :user_id
                  GROUP BY p.id_pedido
                  ORDER BY p.fecha_pedido DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Read single order with details
    public function readOneWithDetails() {
        $query = "SELECT p.*, u.nombre, u.apellido, u.email, u.telefono
                  FROM " . $this->table_name . " p
                  LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                  WHERE p.id_pedido = :id_pedido
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_pedido', $this->id_pedido);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->id_usuario = $row['id_usuario'];
            $this->fecha_pedido = $row['fecha_pedido'];
            $this->fecha_entrega = $row['fecha_entrega'];
            $this->direccion_entrega = $row['direccion_entrega'];
            $this->mensaje_personalizado = $row['mensaje_personalizado'];
            $this->estado = $row['estado'];
            $this->total = $row['total'];
            $this->metodo_pago = $row['metodo_pago'];

            // Get order details
            $details_query = "SELECT dp.*, pr.nombre as producto_nombre, pr.imagen_url
                             FROM detalle_pedido dp
                             LEFT JOIN productos pr ON dp.id_producto = pr.id_producto
                             WHERE dp.id_pedido = :id_pedido";

            $details_stmt = $this->conn->prepare($details_query);
            $details_stmt->bindParam(':id_pedido', $this->id_pedido);
            $details_stmt->execute();

            $details = array();
            while ($detail_row = $details_stmt->fetch(PDO::FETCH_ASSOC)) {
                $details[] = $detail_row;
            }

            return array(
                'order' => $row,
                'details' => $details
            );
        }
        return false;
    }

    // Update order status
    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . "
                  SET estado = :estado
                  WHERE id_pedido = :id_pedido";

        $stmt = $this->conn->prepare($query);

        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->id_pedido = htmlspecialchars(strip_tags($this->id_pedido));

        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':id_pedido', $this->id_pedido);

        return $stmt->execute();
    }

    // Get all orders (admin)
    public function readAll() {
        $query = "SELECT p.*, u.nombre, u.apellido, u.email,
                         COUNT(dp.id_detalle) as total_items
                  FROM " . $this->table_name . " p
                  LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                  LEFT JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
                  GROUP BY p.id_pedido
                  ORDER BY p.fecha_pedido DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
