<?php
class SesionApoyo {
    private $conn;
    private $table_name = "sesiones_apoyo";

    public $id_sesion;
    public $id_usuario;
    public $tipo_apoyo;
    public $descripcion_situacion;
    public $consejo_brindado;
    public $fecha_sesion;
    public $duracion_minutos;
    public $calificacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new support session
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET id_usuario=:id_usuario, tipo_apoyo=:tipo_apoyo,
                      descripcion_situacion=:descripcion_situacion,
                      consejo_brindado=:consejo_brindado, duracion_minutos=:duracion_minutos";

        $stmt = $this->conn->prepare($query);

        // Sanitize inputs
        $this->id_usuario = htmlspecialchars(strip_tags($this->id_usuario));
        $this->tipo_apoyo = htmlspecialchars(strip_tags($this->tipo_apoyo));
        $this->descripcion_situacion = htmlspecialchars(strip_tags($this->descripcion_situacion));
        $this->consejo_brindado = htmlspecialchars(strip_tags($this->consejo_brindado));
        $this->duracion_minutos = htmlspecialchars(strip_tags($this->duracion_minutos));

        // Bind values
        $stmt->bindParam(":id_usuario", $this->id_usuario);
        $stmt->bindParam(":tipo_apoyo", $this->tipo_apoyo);
        $stmt->bindParam(":descripcion_situacion", $this->descripcion_situacion);
        $stmt->bindParam(":consejo_brindado", $this->consejo_brindado);
        $stmt->bindParam(":duracion_minutos", $this->duracion_minutos);

        if($stmt->execute()) {
            $this->id_sesion = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Read sessions by user
    public function readByUser($user_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE id_usuario = :user_id
                  ORDER BY fecha_sesion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Read all sessions (admin)
    public function readAll() {
        $query = "SELECT s.*, u.nombre, u.apellido, u.email
                  FROM " . $this->table_name . " s
                  LEFT JOIN usuarios u ON s.id_usuario = u.id_usuario
                  ORDER BY s.fecha_sesion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Update session with advice
    public function updateAdvice() {
        $query = "UPDATE " . $this->table_name . "
                  SET consejo_brindado = :consejo_brindado,
                      duracion_minutos = :duracion_minutos
                  WHERE id_sesion = :id_sesion";

        $stmt = $this->conn->prepare($query);

        $this->consejo_brindado = htmlspecialchars(strip_tags($this->consejo_brindado));
        $this->duracion_minutos = htmlspecialchars(strip_tags($this->duracion_minutos));
        $this->id_sesion = htmlspecialchars(strip_tags($this->id_sesion));

        $stmt->bindParam(':consejo_brindado', $this->consejo_brindado);
        $stmt->bindParam(':duracion_minutos', $this->duracion_minutos);
        $stmt->bindParam(':id_sesion', $this->id_sesion);

        return $stmt->execute();
    }

    // Rate session
    public function rateSession() {
        $query = "UPDATE " . $this->table_name . "
                  SET calificacion = :calificacion
                  WHERE id_sesion = :id_sesion";

        $stmt = $this->conn->prepare($query);

        $this->calificacion = htmlspecialchars(strip_tags($this->calificacion));
        $this->id_sesion = htmlspecialchars(strip_tags($this->id_sesion));

        $stmt->bindParam(':calificacion', $this->calificacion);
        $stmt->bindParam(':id_sesion', $this->id_sesion);

        return $stmt->execute();
    }

    // Get session statistics
    public function getStatistics() {
        $query = "SELECT
                    tipo_apoyo,
                    COUNT(*) as total_sesiones,
                    AVG(calificacion) as calificacion_promedio,
                    AVG(duracion_minutos) as duracion_promedio
                  FROM " . $this->table_name . "
                  WHERE calificacion IS NOT NULL
                  GROUP BY tipo_apoyo";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
