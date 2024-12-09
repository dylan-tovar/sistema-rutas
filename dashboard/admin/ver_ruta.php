<?php
// Conexión a la base de datos
include '../../db/conexion.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener el ID de la ruta desde la URL
$id_ruta = isset($_GET['id_ruta']) ? intval($_GET['id_ruta']) : 0;

if ($id_ruta <= 0) {
    die("ID de ruta no válido.");
}

// Consultar la ruta y los pedidos asociados
$query_ruta = "
    SELECT rp.orden_parada, p.latitud, p.longitud, d.nombre_direccion, u.nombre AS usuario
    FROM rutas_pedidos rp
    JOIN pedidos p ON rp.id_pedido = p.id
    JOIN direcciones d ON p.id_direccion = d.id
    JOIN usuarios u ON p.id_cliente = u.id
    WHERE rp.id_ruta = ?
    ORDER BY rp.orden_parada ASC
";
$stmt = $mysqli->prepare($query_ruta);
$stmt->bind_param("i", $id_ruta);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("No se encontraron datos para la ruta con ID $id_ruta.");
}

$direcciones = [];
while ($row = $result->fetch_assoc()) {
    $direcciones[] = [
        "lat" => floatval($row['latitud']),
        "lon" => floatval($row['longitud']),
        "direccion" => $row['nombre_direccion'],
        "usuario" => $row['usuario']
    ];
}

// Verificar los datos recuperados
echo "<pre>";
print_r($direcciones);
echo "</pre>";

// Cerrar la conexión
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Ruta <?php echo $id_ruta; ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.min.js"></script>
</head>
<body>

<h2>Ruta Generada para ID de Ruta: <?php echo $id_ruta; ?></h2>

<!-- Añadimos el mapa -->
<div id="map" style="width: 100%; height: 500px; border: 1px solid black;"></div>

<script>
    // Asegurarse de que hay al menos un punto para mostrar el mapa
    if (<?php echo count($direcciones); ?> > 0) {
        // Depuración: Ver si los scripts de Leaflet se están cargando correctamente
        console.log('Iniciando el mapa con Leaflet...');

        // Inicializar el mapa
        var map = L.map('map').setView([<?php echo $direcciones[0]['lat']; ?>, <?php echo $direcciones[0]['lon']; ?>], 13);

        // Añadir capa de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Crear los puntos de la ruta
        var waypoints = [
            <?php foreach ($direcciones as $direccion) {
                echo "L.latLng({$direccion['lat']}, {$direccion['lon']}),";
            } ?>
        ];

        console.log(waypoints); // Depuración: Verificar si los puntos se están generando correctamente

        // Añadir la ruta en el mapa
        L.Routing.control({
            waypoints: waypoints,
            createMarker: function(i, wp) {
                return L.marker(wp.latLng).bindPopup("<?php echo $direcciones[$i]['direccion']; ?><br>Cliente: <?php echo $direcciones[$i]['usuario']; ?>");
            },
            lineOptions: {
                styles: [{color: 'blue', opacity: 0.7, weight: 4}]
            },
            routeWhileDragging: false,
            addWaypoints: false
        }).addTo(map);
    } else {
        console.log("No se encontraron puntos para la ruta.");
    }
</script>

</body>
</html>