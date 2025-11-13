<?php
class DetallePedido {
    private $conn;
    private $table_name = "detalle_pedido";

    public $id_detalle;
    public $id_pedido;
    public $id_producto;
    public $cantidad;
    public $precio_unitario;
    public $subtotal;
    public $mensaje_especial;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create order detail
    public function crear() {
        $this->subtotal = $this->cantidad * $this->precio_unitario;

        $query = "INSERT INTO " . $this->table_name . "
                  SET id_pedido=:id_pedido, id_producto=:id_producto,
                      cantidad=:cantidad, precio_unitario=:precio_unitario,
                      subtotal=:subtotal";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->id_pedido = htmlspecialchars(strip_tags($this->id_pedido));
        $this->id_producto = htmlspecialchars(strip_tags($this->id_producto));
        $this->cantidad = htmlspecialchars(strip_tags($this->cantidad));
        $this->precio_unitario = htmlspecialchars(strip_tags($this->precio_unitario));
        $this->subtotal = htmlspecialchars(strip_tags($this->subtotal));

        // Bind values
        $stmt->bindParam(":id_pedido", $this->id_pedido);
        $stmt->bindParam(":id_producto", $this->id_producto);
        $stmt->bindParam(":cantidad", $this->cantidad);
        $stmt->bindParam(":precio_unitario", $this->precio_unitario);
        $stmt->bindParam(":subtotal", $this->subtotal);

        return $stmt->execute();
    }

    // Read details by order
    public function readByOrder($order_id) {
        $query = "SELECT dp.*, p.nombre as producto_nombre, p.imagen_url, p.mensaje_emocional
                  FROM " . $this->table_name . " dp
                  LEFT JOIN productos p ON dp.id_producto = p.id_producto
                  WHERE dp.id_pedido = :order_id
                  ORDER BY dp.id_detalle";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt;
    }
}
?>
