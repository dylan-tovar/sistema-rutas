<?php
// Incluir la conexión a la base de datos
include '../../db/conexion.php';

$id_ruta = $_GET['id_ruta'] ?? null;
$distancia_total = $_GET['distancia_total'] ?? 0;

if (!$id_ruta) {
    // Si no se proporciona una ruta específica, mostrar un menú de rutas pendientes
    $query_rutas_pendientes = "
        SELECT r.id, r.fecha_creacion, r.distancia_total, u.nombre AS repartidor
        FROM rutas r
        LEFT JOIN usuarios u ON r.id_repartidor = u.id
        WHERE r.estado = 'pendiente'
        ORDER BY r.fecha_creacion DESC";
    $rutas_pendientes = $mysqli->query($query_rutas_pendientes);
} else {
    // Si se proporciona un ID de ruta, mostrar los detalles de esa ruta
    // Obtener la información de la ruta, incluyendo la distancia total
    $query_ruta = "
        SELECT r.distancia_total, u.nombre AS repartidor, v.modelo AS vehiculo
        FROM rutas r
        LEFT JOIN usuarios u ON r.id_repartidor = u.id
        LEFT JOIN vehiculos v ON r.vehiculos_utilizados = v.id
        WHERE r.id = $id_ruta";
    $ruta_result = $mysqli->query($query_ruta);
    $ruta_data = $ruta_result->fetch_assoc();
    $distancia_total = $ruta_data['distancia_total'];

    // Obtener las paradas de la ruta
    $query_paradas = "
        SELECT p.id, p.latitud, p.longitud, p.estado 
        FROM rutas_pedidos rp
        JOIN pedidos p ON rp.id_pedido = p.id
        WHERE rp.id_ruta = $id_ruta
        ORDER BY rp.orden_parada";
    $paradas_result = $mysqli->query($query_paradas);

    $paradas = [];
    while ($row = $paradas_result->fetch_assoc()) {
        $paradas[] = $row;
    }

    // Obtener lista de repartidores disponibles
    $repartidor_query = "
        SELECT u.id, u.nombre 
        FROM usuarios u
        WHERE u.id_tipo = 3
        AND u.id NOT IN (SELECT id_usuario_asignado FROM vehiculos WHERE id_usuario_asignado IS NOT NULL)";
    $repartidores = $mysqli->query($repartidor_query);

    // Obtener lista de vehículos disponibles
    $vehiculo_query = "SELECT id, modelo FROM vehiculos WHERE id_usuario_asignado IS NULL";
    $vehiculos = $mysqli->query($vehiculo_query);
}

// Si se ha enviado el formulario para asignar un repartidor y un vehículo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id_ruta) {
    $id_repartidor = $_POST['repartidor'] ?? null;
    $id_vehiculo = $_POST['vehiculo'] ?? null;

    if ($id_repartidor && $id_vehiculo) {
        // Actualizar la ruta con el repartidor y el vehículo asignado
        $update_query = "UPDATE rutas SET id_repartidor = $id_repartidor, vehiculos_utilizados = $id_vehiculo WHERE id = $id_ruta";
        if ($mysqli->query($update_query)) {
            // Asignar el usuario al vehículo
            $update_vehiculo = "UPDATE vehiculos SET id_usuario_asignado = $id_repartidor WHERE id = $id_vehiculo";
            $mysqli->query($update_vehiculo);
            echo "<p>Ruta asignada exitosamente al repartidor y vehículo.</p>";
        } else {
            echo "<p>Error al asignar la ruta.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruta Optimizada</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.2.2/dist/cdn.min.js"></script> <!-- Incluyendo Alpine.js -->
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
</head>
<body>
    <section class="flex">
        <?php include '_components/header&aside.php'; ?>
    </section>

<?php if (!$id_ruta): ?>
    <h1>Rutas Pendientes</h1>

    <?php if ($rutas_pendientes->num_rows > 0): ?>
        <ul>
            <?php while ($ruta = $rutas_pendientes->fetch_assoc()): ?>
                <li>
                    <a href="?id_ruta=<?php echo $ruta['id']; ?>">
                        <strong>ID Ruta:</strong> <?php echo $ruta['id']; ?> | 
                        <strong>Repartidor:</strong> <?php echo $ruta['repartidor'] ?: 'No asignado'; ?> |
                        <strong>Fecha:</strong> <?php echo $ruta['fecha_creacion']; ?> |
                        <strong>Distancia Total:</strong> <?php echo $ruta['distancia_total'] / 1000; ?> km
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No hay rutas pendientes en este momento.</p>
    <?php endif; ?>

<?php else: ?>

<h1>Mapa de la Ruta Optimizada</h1>

<div id="map"></div>

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

<script>
// Crear el mapa centrado en las coordenadas iniciales (por ejemplo, el depot)
var map = L.map('map').setView([10.500000, -66.916664], 13);

// Añadir capa de OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Coordenadas de las paradas (agregar desde PHP)
var paradas = <?php echo json_encode($paradas); ?>;

// Crear un array de waypoints (paradas) para el enrutamiento
var waypoints = paradas.map(parada => L.latLng(parada.latitud, parada.longitud));

// Configurar el servicio de enrutamiento usando OSRM
L.Routing.control({
    waypoints: waypoints, // Añadir los puntos de parada
    routeWhileDragging: false, // No recalcular ruta mientras se arrastra
    router: L.Routing.osrmv1({
        language: 'es', // Idioma español
        profile: 'car'  // Perfil de enrutamiento (puede ser 'car', 'bike', etc.)
    }),
    showAlternatives: false,
    addWaypoints: false,  // Desactivar la opción de añadir nuevos puntos en el mapa
    draggableWaypoints: false  // No permitir arrastrar las paradas
}).addTo(map);
</script>

<h2>Resumen de la Ruta</h2>
<p><strong>Distancia Total Recorrida:</strong> <?php echo $distancia_total / 1000; ?> km</p>

<h3>Paradas:</h3>
<ul>
    <?php foreach ($paradas as $parada): ?>
        <li>ID Pedido: <?php echo $parada['id']; ?>, Latitud: <?php echo $parada['latitud']; ?>, Longitud: <?php echo $parada['longitud']; ?>, Estado: <?php echo $parada['estado']; ?></li>
    <?php endforeach; ?>
</ul>

<h2>Asignar Repartidor y Vehículo a la Ruta</h2>

<form method="POST" action="">
    <label for="repartidor">Seleccionar Repartidor:</label>
    <select name="repartidor" id="repartidor" required>
        <option value="">Seleccione un repartidor</option>
        <?php while ($repartidor = $repartidores->fetch_assoc()): ?>
            <option value="<?php echo $repartidor['id']; ?>"><?php echo $repartidor['nombre']; ?></option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label for="vehiculo">Seleccionar Vehículo:</label>
    <select name="vehiculo" id="vehiculo" required>
        <option value="">Seleccione un vehículo</option>
        <?php while ($vehiculo = $vehiculos->fetch_assoc()): ?>
            <option value="<?php echo $vehiculo['id']; ?>"><?php echo $vehiculo['modelo']; ?></option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <button type="submit">Asignar Repartidor y Vehículo</button>
</form>

<?php endif; ?>

</body>
</html>