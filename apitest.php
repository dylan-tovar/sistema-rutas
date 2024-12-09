<?php
// Conexión a la base de datos
include 'db/conexion.php';

// Mostrar errores de PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definir manualmente el depot
$depot = [
    "lat" => 10.500000, // Cambia a las coordenadas reales del depot
    "lon" => -66.916664, // Cambia a las coordenadas reales del depot
    "direccion" => "Depot Central", // Nombre del depot
    "usuario" => "Sistema" // Se puede marcar como agregado por el sistema
];

// Consultar las direcciones y los usuarios desde la base de datos
$query = "SELECT p.latitud, p.longitud, d.nombre_direccion AS direccion, u.nombre AS usuario
          FROM pedidos p
          JOIN direcciones d ON p.id_direccion = d.id
          JOIN usuarios u ON p.id_cliente = u.id
          WHERE p.estado = 'pendiente'";
$result = $mysqli->query($query);

$direcciones = [$depot]; // Comenzamos con el depot como el primer punto
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $direcciones[] = [
            "lat" => floatval($row['latitud']),
            "lon" => floatval($row['longitud']),
            "direccion" => $row['direccion'],
            "usuario" => $row['usuario']
        ];
    }
}

// Optimización: Reducir el cálculo a solo 1 vehículo
$vehiculos_disponibles = 1; // Solo un vehículo para optimización

// Asegurarse de que hay al menos dos direcciones (incluyendo el depot)
if (count($direcciones) < 3) { // Deben haber al menos dos direcciones además del depot
    die("Se requieren al menos dos direcciones para optimizar una ruta.");
}

// URL de la API para optimizar la ruta
$urlApiOptimizarRuta = 'http://127.0.0.1:5000/optimizar_ruta';
$data = [
    "direcciones" => $direcciones,
    "vehiculos_disponibles" => $vehiculos_disponibles // Enviar la cantidad de vehículos disponibles a la API
];

// Inicializar cURL
$ch = curl_init($urlApiOptimizarRuta);

// Configurar cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Ejecutar la solicitud y obtener la respuesta de optimización de ruta
$response = curl_exec($ch);

// Verificar errores de cURL
if (curl_errno($ch)) {
    echo 'Error en la conexión: ' . curl_error($ch);
    exit;
}

$result = json_decode($response, true);

// Validar que haya una ruta en la respuesta
if (!isset($result['rutas'])) {
    die("No se pudo calcular la ruta optimizada.");
}

// Mostrar el mapa con la ruta y los datos debajo
mostrarMapa($direcciones, $result['rutas'], $result['distancia_total']);

// Función para mostrar el mapa y la información de la ruta
function mostrarMapa($direcciones, $rutas, $distancia_total) {
    echo "<h2>Ruta Optimizada</h2>";
    echo "<div id='map' style='width: 100%; height: 500px;'></div>"; // Tamaño del mapa ajustable
    echo "<link rel='stylesheet' href='https://unpkg.com/leaflet/dist/leaflet.css' />";
    echo "<script src='https://unpkg.com/leaflet/dist/leaflet.js'></script>";
    echo "<script src='https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.min.js'></script>";
    echo "<script>
        // Inicializar el mapa
        var map = L.map('map').setView([{$direcciones[0]['lat']}, {$direcciones[0]['lon']}], 13);

        // Capa de OpenStreetMap con un estilo optimizado
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: 'Map data © <a href=\"https://openstreetmap.org\">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Colores para los vehículos
        var colors = ['blue', 'red', 'green', 'orange', 'purple'];
        var vehiculoIndex = 0;
    ";

    // Iterar sobre las rutas asignadas a cada vehículo
    foreach ($rutas as $vehiculo => $ruta) {
        if (count($ruta) > 2) { // Solo mostrar si hay más paradas además del depot
            echo "var waypoints = [];";
            echo "var directionsInfo = [];";
            foreach ($ruta as $index) {
                $lat = $direcciones[$index]['lat'];
                $lon = $direcciones[$index]['lon'];
                $direccion = $direcciones[$index]['direccion'];
                $usuario = $direcciones[$index]['usuario'];
                echo "waypoints.push(L.latLng($lat, $lon));";
                echo "directionsInfo.push('Parada: $direccion, Usuario: $usuario');";
            }
            echo "
                L.Routing.control({
                    waypoints: waypoints,
                    routeWhileDragging: false,
                    addWaypoints: false,
                    show: false,
                    createMarker: function(i, wp) {
                        return L.marker(wp.latLng).bindPopup(directionsInfo[i]);
                    },
                    lineOptions: {
                        styles: [{color: colors[vehiculoIndex % colors.length], opacity: 0.7, weight: 4}]
                    }
                }).addTo(map);
                vehiculoIndex++;
            ";
        }
    }

    echo "</script>";

    // Mostrar la distancia total y el orden de las paradas debajo del mapa
    echo "<div><h3>Distancia total: " . ($distancia_total / 1000) . " km</h3>";
    echo "<h4>Orden de las paradas por vehículo:</h4>";
    foreach ($rutas as $vehiculo => $ruta) {
        if (count($ruta) > 2) { // Solo mostrar si hay más paradas además del depot
            echo "<h5>Vehículo " . ($vehiculo + 1) . ":</h5><ol>";
            foreach ($ruta as $index) {
                echo "<li>{$direcciones[$index]['direccion']} (Usuario: {$direcciones[$index]['usuario']})</li>";
            }
            echo "</ol>";
        }
    }
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .leaflet-control-container .leaflet-routing-container-hide {    display: none; }
    </style>    
</head>
<body>
    
</body>
</html>