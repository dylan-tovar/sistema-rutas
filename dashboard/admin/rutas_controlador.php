<?php
// Incluir la conexión a la base de datos
require '../../db/conexion.php';

// Función para obtener rutas pendientes
function obtener_rutas_pendientes($mysqli) {
    $query = "
        SELECT r.id, r.fecha_creacion, r.distancia_total, u.nombre AS repartidor
        FROM rutas r
        LEFT JOIN usuarios u ON r.id_repartidor = u.id
        WHERE r.estado = 'pendiente'
        ORDER BY r.fecha_creacion DESC";
    return $mysqli->query($query);
}

// Función para obtener la información de una ruta por ID
function obtener_ruta_por_id($mysqli, $id_ruta) {
    $query = "
        SELECT r.distancia_total, u.nombre AS repartidor, v.modelo AS vehiculo
        FROM rutas r
        LEFT JOIN usuarios u ON r.id_repartidor = u.id
        LEFT JOIN vehiculos v ON r.vehiculos_utilizados = v.id
        WHERE r.id = $id_ruta";
    return $mysqli->query($query)->fetch_assoc();
}

// Función para obtener las paradas de una ruta
function obtener_paradas_por_ruta($mysqli, $id_ruta) {
    $query = "
        SELECT p.id, p.latitud, p.longitud, p.estado 
        FROM rutas_pedidos rp
        JOIN pedidos p ON rp.id_pedido = p.id
        WHERE rp.id_ruta = $id_ruta
        ORDER BY rp.orden_parada";
    return $mysqli->query($query);
}

// Función para obtener repartidores disponibles
function obtener_repartidores_disponibles($mysqli) {
    $query = "
        SELECT u.id, u.nombre 
        FROM usuarios u
        WHERE u.id_tipo = 3
        AND u.id NOT IN (SELECT id_usuario_asignado FROM vehiculos WHERE id_usuario_asignado IS NOT NULL)";
    return $mysqli->query($query);
}

// Función para obtener vehículos disponibles
function obtener_vehiculos_disponibles($mysqli) {
    $query = "SELECT id, modelo FROM vehiculos WHERE id_usuario_asignado IS NULL";
    return $mysqli->query($query);
}

// Función para asignar repartidor y vehículo a una ruta
function asignar_repartidor_y_vehiculo($mysqli, $id_ruta, $id_repartidor, $id_vehiculo) {
    $update_query = "UPDATE rutas SET id_repartidor = $id_repartidor, vehiculos_utilizados = $id_vehiculo WHERE id = $id_ruta";
    if ($mysqli->query($update_query)) {
        $update_vehiculo = "UPDATE vehiculos SET id_usuario_asignado = $id_repartidor WHERE id = $id_vehiculo";
        return $mysqli->query($update_vehiculo);
    }
    return false;
}
?>