-- Additional table for support request details
CREATE TABLE IF NOT EXISTS solicitudes_apoyo (
    id_solicitud INT PRIMARY KEY AUTO_INCREMENT,
    id_sesion INT NOT NULL,
    urgencia ENUM('baja', 'media', 'alta') DEFAULT 'baja',
    horarios_preferidos JSON,
    edad INT,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado_solicitud ENUM('pendiente', 'contactado', 'programado', 'completado') DEFAULT 'pendiente',
    fecha_contacto TIMESTAMP NULL,
    fecha_programada TIMESTAMP NULL,
    notas_admin TEXT,
    FOREIGN KEY (id_sesion) REFERENCES sesiones_apoyo(id_sesion)
);

-- Insert sample support request for testing
INSERT INTO solicitudes_apoyo (id_sesion, urgencia, horarios_preferidos, edad, estado_solicitud)
VALUES (1, 'media', '["afternoon", "evening"]', 28, 'pendiente');
