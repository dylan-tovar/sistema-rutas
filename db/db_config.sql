-- Base de datos : rutas

-- -----------------------------------------------------------
-- Para el registro de usuarios y roles
-- Estructura tabla de usuarios

CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nombre` varchar(50) NOT NULL,
    `usuario` varchar(50) NOT NULL,
    `correo` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `last_session` datetime NOT NULL,
    `id_tipo` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- Estructura tabla de roles

CREATE TABLE IF NOT EXISTS `tipo_usuario` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tipo` varchar(50) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;



INSERT INTO `tipo_usuario` (`id`, `tipo`) VALUES
(1, 'Administrador'),
(2, 'Usuario'),
(3, 'Repartidor');
-- -----------------------------------------------------------
-- Para el registro de vehiculos

CREATE TABLE IF NOT EXISTS `vehiculos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `placa` varchar(10) NOT NULL,
    `modelo` varchar(50) NOT NULL,
    `marca` varchar(50) NOT NULL,
    `estado` ENUM('disponible', 'en uso', 'no disponible') DEFAULT 'disponible',
    `id_usuario_asignado` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`id_usuario_asignado`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------------------------------------
-- Para el registro de direcciones

CREATE TABLE direcciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_direccion VARCHAR(255) NOT NULL,
    latitud DECIMAL(10,8) NOT NULL,
    longitud DECIMAL(11,8) NOT NULL,
    id_cliente INT,
    FOREIGN KEY (id_cliente) REFERENCES usuarios(id)
);

-- Registro de pedidos
CREATE TABLE pedidos (
    id INT(11) NOT NULL AUTO_INCREMENT,
    id_cliente INT(11) NOT NULL,
    fecha_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'en_proceso', 'completado', 'cancelado') NOT NULL DEFAULT 'pendiente',
    id_direccion INT(11) NOT NULL,
    latitud DECIMAL(10,8) NOT NULL,
    longitud DECIMAL(11,8) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (id_cliente) REFERENCES usuarios(id),
    FOREIGN KEY (id_direccion) REFERENCES direcciones(id)
);
-- -----------------------------------------------------------