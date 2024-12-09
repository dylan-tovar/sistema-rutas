<?php
// Incluir la conexión a la base de datos
include 'db/conexion.php';

// Obtener todas las rutas en proceso
$query = "
    SELECT r.id, r.fecha_creacion, u.nombre AS repartidor, v.modelo AS vehiculo, r.distancia_total
    FROM rutas r
    JOIN usuarios u ON r.id_repartidor = u.id
    JOIN vehiculos v ON r.vehiculos_utilizados = v.id
    WHERE r.estado = 'en_proceso'";
$result = $mysqli->query($query);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoreo de Rutas en Proceso</title>
</head>
<body>

<h1>Monitoreo de Rutas en Proceso</h1>

<table border="1">
    <tr>
        <th>ID Ruta</th>
        <th>Fecha de Creación</th>
        <th>Repartidor</th>
        <th>Vehículo</th>
        <th>Distancia Total (km)</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['fecha_creacion']; ?></td>
        <td><?php echo $row['repartidor']; ?></td>
        <td><?php echo $row['modelo']; ?></td>
        <td><?php echo $row['distancia_total'] / 1000; ?> km</td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>