<?php
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id_usuario;
    public $nombre;
    public $apellido;
    public $email;
    public $telefono;
    public $direccion;
    public $fecha_nacimiento;
    public $tipo_usuario;
    public $password_hash;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET nombre=:nombre, apellido=:apellido, email=:email,
                      telefono=:telefono, direccion=:direccion,
                      fecha_nacimiento=:fecha_nacimiento, tipo_usuario=:tipo_usuario,
                      password_hash=:password_hash";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido = htmlspecialchars(strip_tags($this->apellido));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telefono = htmlspecialchars(strip_tags($this->telefono));
        $this->direccion = htmlspecialchars(strip_tags($this->direccion));

        // Bind values
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":apellido", $this->apellido);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":direccion", $this->direccion);
        $stmt->bindParam(":fecha_nacimiento", $this->fecha_nacimiento);
        $stmt->bindParam(":tipo_usuario", $this->tipo_usuario);
        $stmt->bindParam(":password_hash", $this->password_hash);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read user by email
    public function readByEmail() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->id_usuario = $row['id_usuario'];
            $this->nombre = $row['nombre'];
            $this->apellido = $row['apellido'];
            $this->telefono = $row['telefono'];
            $this->direccion = $row['direccion'];
            $this->fecha_nacimiento = $row['fecha_nacimiento'];
            $this->tipo_usuario = $row['tipo_usuario'];
            $this->password_hash = $row['password_hash'];
            $this->estado = $row['estado'];
            return true;
        }
        return false;
    }

    // Verify password
    public function verifyPassword($password) {
        return password_verify($password, $this->password_hash);
    }

    // Hash password
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function obtenerEstadisticas() {
        $stats = [];

        // Total Pedidos y Gasto
        $query_pedidos = "SELECT COUNT(*) as total_pedidos, COALESCE(SUM(total), 0) as total_gastado FROM pedidos WHERE id_usuario = :id_usuario";
        $stmt_pedidos = $this->conn->prepare($query_pedidos);
        $stmt_pedidos->bindParam(':id_usuario', $this->id_usuario);
        $stmt_pedidos->execute();
        $stats['pedidos'] = $stmt_pedidos->fetch(PDO::FETCH_ASSOC);

        // Total Sesiones de Apoyo
        $query_sesiones = "SELECT COUNT(*) as total_sesiones FROM sesiones_apoyo WHERE id_usuario = :id_usuario";
        $stmt_sesiones = $this->conn->prepare($query_sesiones);
        $stmt_sesiones->bindParam(':id_usuario', $this->id_usuario);
        $stmt_sesiones->execute();
        $stats['sesiones'] = $stmt_sesiones->fetch(PDO::FETCH_ASSOC);

        return $stats;
    }

    public function readById() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_usuario = :id_usuario LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $this->id_usuario);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->nombre = $row['nombre'];
            $this->apellido = $row['apellido'];
            $this->email = $row['email'];
            $this->telefono = $row['telefono'];
            $this->direccion = $row['direccion'];
            $this->fecha_nacimiento = $row['fecha_nacimiento'];
            $this->tipo_usuario = $row['tipo_usuario'];
            $this->password_hash = $row['password_hash'];
            $this->estado = $row['estado'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET
                      nombre = :nombre,
                      apellido = :apellido,
                      email = :email,
                      telefono = :telefono,
                      direccion = :direccion
                  WHERE
                      id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->nombre=htmlspecialchars(strip_tags($this->nombre));
        $this->apellido=htmlspecialchars(strip_tags($this->apellido));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->telefono=htmlspecialchars(strip_tags($this->telefono));
        $this->direccion=htmlspecialchars(strip_tags($this->direccion));
        $this->id_usuario=htmlspecialchars(strip_tags($this->id_usuario));

        // Bind
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':direccion', $this->direccion);
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function updateSaldo($monto) {
        $query = "UPDATE " . $this->table_name . "
                  SET saldo = saldo + :monto
                  WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':monto', $monto);
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fecha_registro DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function updateEstado() {
        $query = "UPDATE " . $this->table_name . "
                  SET estado = :estado
                  WHERE id_usuario = :id_usuario";

        $stmt = $this->conn->prepare($query);

        $this->estado = htmlspecialchars(strip_tags($this->estado));
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));

        $stmt->bindParam(':estado', $this->estado);
        $stmt->bindParam(':id_usuario', $this->id_usuario);

        if($stmt->execute()){
            return true;
        }
        return false;
    }
}
?>
