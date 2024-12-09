<?php
    session_start();
    ob_start();  // Inicia el búfer de salida para evitar problemas con headers
    require '../../db/conexion.php';

    if (!isset($_SESSION['id_usuario'])) {
        header("Location: index.php");
        exit;
    }
    
    $idUsuario = $_SESSION['id_usuario'];
    $sql = "SELECT * FROM usuarios WHERE id = '$idUsuario'";
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();

    // Obtener ID de ruta
    $id_ruta = $_GET['id_ruta'] ?? null;

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
        // Obtener la información de la ruta, incluyendo la distancia total directamente de la base de datos
        $query_ruta = "
            SELECT r.distancia_total, u.nombre AS repartidor, v.modelo AS vehiculo
            FROM rutas r
            LEFT JOIN usuarios u ON r.id_repartidor = u.id
            LEFT JOIN vehiculos v ON r.vehiculos_utilizados = v.id
            WHERE r.id = $id_ruta";
        $ruta_result = $mysqli->query($query_ruta);
        $ruta_data = $ruta_result->fetch_assoc();
        $distancia_total = $ruta_data['distancia_total'] ?? 0;

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

    // Si se ha enviado el formulario para asignar repartidor y vehículo
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id_ruta) {
        $id_repartidor = $_POST['repartidor'] ?? null;
        $id_vehiculo = $_POST['vehiculo'] ?? null;
    
        if ($id_repartidor && $id_vehiculo) {
            // Actualizar la ruta con el repartidor y el vehículo asignado
            $update_query = "UPDATE rutas SET id_repartidor = $id_repartidor, vehiculos_utilizados = $id_vehiculo WHERE id = $id_ruta";
            if ($mysqli->query($update_query)) {
                // Asignar el usuario al vehículo y cambiar su estado a "en uso"
                $update_vehiculo = "UPDATE vehiculos SET id_usuario_asignado = $id_repartidor, estado = 'en uso' WHERE id = $id_vehiculo";
                $mysqli->query($update_vehiculo);
                
                echo "<p>Ruta asignada exitosamente al repartidor y vehículo.</p>";
            } else {
                echo "<p>Error al asignar la ruta.</p>";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.2.2/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Header y Aside -->
    <section class="flex">
        <?php include '_components/header&aside.php'; ?>
    </section>

    <!-- Container principal -->
    <div class="ml-64 mt-20 p-4">

    <?php if (!$id_ruta): ?>
        <!-- Listado de rutas pendientes -->
        <div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
            <div class="text-xl font-semibold text-gray-800">
                Visualizar las Rutas Pendientes
                <p class="text-gray-500 text-sm mt-1 font-normal">
                    Seleccione una ruta para ver los detalles y asignar un repartidor y vehículo.
                </p>
            </div>

            <!-- Tabla de rutas pendientes -->
            <div class="p-4">
            <?php if ($rutas_pendientes->num_rows > 0): ?>
                <ul class="divide-y divide-gray-300">
                    <?php while ($ruta = $rutas_pendientes->fetch_assoc()): ?>
                        <li class="flex justify-between items-center p-4 hover:bg-gray-50 transition">
                            <a class="text-base font-semibold text-blue-700 hover:underline" href="?id_ruta=<?php echo $ruta['id']; ?>">
                                <strong>ID Ruta:</strong> <?php echo $ruta['id']; ?> 
                            </a>
                            <span class="text-sm text-gray-500">
                                <strong>Repartidor:</strong> <?php echo $ruta['repartidor'] ?: 'No asignado'; ?> |
                                <strong>Fecha:</strong> <?php echo $ruta['fecha_creacion']; ?> |
                                <strong>Distancia Total:</strong> <?php echo $ruta['distancia_total'] / 1000; ?> km
                            </span>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-500">No hay rutas pendientes en este momento.</p>
            <?php endif; ?>
            </div>
        </div>

    <?php else: ?>
        <!-- Detalles de la ruta seleccionada -->
        <div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
            <div class="text-xl font-semibold text-gray-800">
                Mapa de la Ruta
                <p class="text-gray-500 text-sm mt-1 font-normal">
                    Visualice la ruta en el mapa y asigne un repartidor y vehículo a la ruta.
                </p>
            </div>
            
            <!-- Mapa de la ruta -->
            <div class="p-5">
                <div id="map"></div>
            </div>

            <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
            <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

            <script>
    var map = L.map('map').setView([10.500000, -66.916664], 13);

    // Agregar capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Definir el depot como el primer waypoint
    var depot = L.latLng(10.500000, -66.916664); // Coordenadas del depot
    var paradas = <?php echo json_encode($paradas); ?>;
    
    // Crear array de waypoints con el depot como primer elemento
    var waypoints = [depot].concat(paradas.map(parada => L.latLng(parada.latitud, parada.longitud)));

    // Crear la ruta en el mapa incluyendo el depot y las paradas
    L.Routing.control({
        waypoints: waypoints,
        routeWhileDragging: false,
        router: L.Routing.osrmv1({
            language: 'es',
            profile: 'car'
        }),
        showAlternatives: false,
        addWaypoints: false,
        draggableWaypoints: false,
        lineOptions: { // Aquí especificamos el color de la línea
            styles: [{ color: 'blue', opacity: 0.8, weight: 6 }]
        }
    }).addTo(map);
</script>

            <!-- Detalles de la ruta -->
            <div class="p-3">
                <div class="text-xl font-semibold text-gray-800">
                    Detalles de la Ruta
                    <p class="text-base mt-1"><strong>Distancia Total Recorrida:</strong> <?php echo $distancia_total / 1000; ?> km</p>
                </div>

                <p class="my-2 text-lg">Paradas:</p>
                <ul class="divide-y divide-gray-300 bg-gray-50 rounded-lg shadow-md">
                    <?php foreach ($paradas as $parada): ?>
                        <li class="flex justify-between items-center py-4 px-6 hover:bg-gray-100 transition">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-500">ID Pedido: <strong class="text-gray-900"><?php echo $parada['id']; ?></strong></span>
                                <span class="text-sm text-gray-500">Latitud: <strong class="text-gray-900"><?php echo $parada['latitud']; ?></strong></span>
                                <span class="text-sm text-gray-500">Longitud: <strong class="text-gray-900"><?php echo $parada['longitud']; ?></strong></span>
                                <span class="text-sm text-gray-500">Estado: <strong class="text-gray-900"><?php echo $parada['estado']; ?></strong></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Formulario para asignar repartidor y vehículo -->
                <div class="pt-6">
                    <h2 class="text-lg font-semibold py-2">Asignar Repartidor y Vehículo a la Ruta</h2>
                    <form method="POST" action="" class="space-y-4">
                        <div class="flex flex-col space-y-2">
                            <label for="repartidor" class="font-medium">Seleccionar Repartidor:</label>
                            <select name="repartidor" id="repartidor" required class="p-2 border border-gray-300 rounded-lg">
                                <option value="">Seleccione un repartidor</option>
                                <?php while ($repartidor = $repartidores->fetch_assoc()): ?>
                                    <option value="<?php echo $repartidor['id']; ?>"><?php echo $repartidor['nombre']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                                
                        <div class="flex flex-col space-y-2">
                            <label for="vehiculo" class="font-medium">Seleccionar Vehículo:</label>
                            <select name="vehiculo" id="vehiculo" required class="p-2 border border-gray-300 rounded-lg">
                                <option value="">Seleccione un vehículo</option>
                                <?php while ($vehiculo = $vehiculos->fetch_assoc()): ?>
                                    <option value="<?php echo $vehiculo['id']; ?>"><?php echo $vehiculo['modelo']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>  
                        
                        <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75">
                            Asignar Repartidor y Vehículo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>   
    </div>
</body>
</html>

<?php ob_end_flush(); ?>