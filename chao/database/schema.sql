-- LIVE Database Schema
-- Emotional Dessert Business with Psychological Support

-- Users table (clients and admin)
CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    direccion TEXT,
    fecha_nacimiento DATE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipo_usuario ENUM('cliente', 'admin') DEFAULT 'cliente',
    password_hash VARCHAR(255) NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo'
);

-- Emotional categories for products and support
CREATE TABLE categorias_emocionales (
    id_categoria INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    color_hex VARCHAR(7) DEFAULT '#FF69B4',
    icono VARCHAR(50)
);

-- Products with emotional focus
CREATE TABLE productos (
    id_producto INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    id_categoria_emocional INT,
    imagen_url VARCHAR(255),
    mensaje_emocional TEXT,
    disponible BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria_emocional) REFERENCES categorias_emocionales(id_categoria)
);

-- Orders
CREATE TABLE pedidos (
    id_pedido INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega DATE,
    direccion_entrega TEXT,
    mensaje_personalizado TEXT,
    estado ENUM('pendiente', 'confirmado', 'preparando', 'entregado', 'cancelado') DEFAULT 'pendiente',
    total DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta', 'transferencia') DEFAULT 'efectivo',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Order details
CREATE TABLE detalle_pedido (
    id_detalle INT PRIMARY KEY AUTO_INCREMENT,
    id_pedido INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    mensaje_especial TEXT,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- Psychological support sessions
CREATE TABLE sesiones_apoyo (
    id_sesion INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    tipo_apoyo ENUM('celebracion', 'estres', 'tristeza', 'ansiedad', 'motivacion') NOT NULL,
    descripcion_situacion TEXT,
    consejo_brindado TEXT,
    fecha_sesion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    duracion_minutos INT DEFAULT 15,
    calificacion INT CHECK (calificacion >= 1 AND calificacion <= 5),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Personalized messages library
CREATE TABLE mensajes_personalizados (
    id_mensaje INT PRIMARY KEY AUTO_INCREMENT,
    id_categoria_emocional INT,
    titulo VARCHAR(100),
    contenido TEXT NOT NULL,
    ocasion VARCHAR(100),
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_categoria_emocional) REFERENCES categorias_emocionales(id_categoria)
);

-- Insert initial emotional categories
INSERT INTO categorias_emocionales (nombre, descripcion, color_hex, icono) VALUES
('Celebración', 'Para momentos de alegría y festividad', '#FFD700', 'celebration'),
('Consuelo', 'Para brindar apoyo en momentos difíciles', '#87CEEB', 'heart'),
('Motivación', 'Para inspirar y dar energía', '#FF6347', 'star'),
('Amor', 'Para expresar cariño y afecto', '#FF1493', 'love'),
('Gratitud', 'Para agradecer y reconocer', '#98FB98', 'thanks');

-- Insert sample products
INSERT INTO productos (nombre, descripcion, precio, id_categoria_emocional, mensaje_emocional, imagen_url) VALUES
('Torta de Celebración Dorada', 'Deliciosa torta con capas de vainilla y crema de mantequilla, decorada con detalles dorados', 45.00, 1, 'Cada momento especial merece ser celebrado con dulzura y alegría', 'public/golden-celebration-cake-with-vanilla-layers.jpg'),
('Cupcakes de Consuelo', 'Suaves cupcakes de chocolate con cobertura de crema y un toque de canela reconfortante', 25.00, 2, 'En los momentos difíciles, un pequeño dulce puede traer gran consuelo al corazón', 'public/comforting-chocolate-cupcakes-with-cream-frosting.jpg'),
('Brownies Energizantes', 'Brownies con nueces y chocolate amargo, perfectos para darte la energía que necesitas', 30.00, 3, 'Cada bocado te recuerda que tienes la fuerza para lograr todo lo que te propongas', 'public/energizing-brownies-with-nuts-and-dark-chocolate.jpg');

-- Insert sample personalized messages
INSERT INTO mensajes_personalizados (id_categoria_emocional, titulo, contenido, ocasion) VALUES
(1, 'Feliz Cumpleaños', 'Que este nuevo año de vida esté lleno de dulces momentos y experiencias maravillosas', 'Cumpleaños'),
(2, 'Apoyo en Momentos Difíciles', 'Recuerda que después de la tormenta siempre sale el sol. Estás más fuerte de lo que crees', 'Duelo o tristeza'),
(3, 'Motivación Diaria', 'Cada día es una nueva oportunidad para brillar. Tú tienes todo lo necesario para triunfar', 'Motivación personal'),
(4, 'Expresión de Amor', 'El amor se siente en cada detalle, en cada gesto dulce que compartimos juntos', 'Aniversario o San Valentín'),
(5, 'Gratitud Especial', 'Gracias por ser esa persona especial que ilumina los días con su presencia', 'Agradecimiento');
