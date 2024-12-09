<?php
include '../../db/conexion.php';

// Habilitar el búfer de salida para evitar problemas con headers
ob_start();

// Manejar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener todos los pedidos pendientes de la base de datos
$query = "SELECT id, latitud, longitud FROM pedidos WHERE estado = 'pendiente'";
$result = $mysqli->query($query);

// Definir el depot (puedes cambiar las coordenadas según tu necesidad)
$depot = [
    'lat' => 10.500000,
    'lon' => -66.916664
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Datos enviados desde el formulario
    $pedidos_seleccionados = isset($_POST['pedidos']) ? $_POST['pedidos'] : [];
    $vehiculos_disponibles = 1;  // Usar un solo vehículo para el cálculo de la ruta

    // Obtener el id del repartidor
    $repartidor_query = "SELECT id FROM usuarios WHERE id_tipo = '3' LIMIT 1";  // ID del repartidor (ajustar si es necesario)
    $repartidor_result = $mysqli->query($repartidor_query);
    $repartidor_data = $repartidor_result->fetch_assoc();
    $id_repartidor = $repartidor_data['id'];
    
    $direcciones = [];
    $ids_pedidos = [];

    // Agregar el depot como la primera dirección
    $direcciones[] = [
        'lat' => $depot['lat'],
        'lon' => $depot['lon']
    ];

    // Añadir los pedidos seleccionados al cálculo manual o automático
    if (isset($_POST['calcular_manual']) || isset($_POST['calculo_automatico'])) {
        if (isset($_POST['calcular_manual'])) {
            // Cálculo manual
            foreach ($pedidos_seleccionados as $id_pedido) {
                $pedido_query = "SELECT id, latitud, longitud FROM pedidos WHERE id = $id_pedido";
                $pedido_result = $mysqli->query($pedido_query);
                $pedido_data = $pedido_result->fetch_assoc();
                $direcciones[] = [
                    'lat' => $pedido_data['latitud'],
                    'lon' => $pedido_data['longitud']
                ];
                $ids_pedidos[] = $pedido_data['id'];
            }
        } elseif (isset($_POST['calculo_automatico'])) {
            // Cálculo automático con todos los pedidos pendientes
            while ($pedido = $result->fetch_assoc()) {
                $direcciones[] = [
                    'lat' => $pedido['latitud'],
                    'lon' => $pedido['longitud']
                ];
                $ids_pedidos[] = $pedido['id'];
            }
        }

        // Llamar a la API para optimizar la ruta
        $data = [
            'direcciones' => $direcciones,
            'vehiculos_disponibles' => $vehiculos_disponibles
        ];
        $response = llamar_api_optimizar_ruta($data);

        if (!isset($response['error'])) {
            // Guardar los datos de la ruta y las paradas en la base de datos, incluyendo la distancia total
            $distancia_total = $response['distancia_total'] ?? 0;

            $query_ruta = "INSERT INTO rutas (fecha_creacion, estado, id_repartidor, vehiculos_utilizados, distancia_total) 
                           VALUES (NOW(), 'pendiente', $id_repartidor, $vehiculos_disponibles, $distancia_total)";
            $mysqli->query($query_ruta);
            $id_ruta = $mysqli->insert_id;

            foreach ($response['rutas'] as $vehiculo_id => $ruta) {
                foreach ($ruta as $orden => $parada) {
                    $id_pedido = $ids_pedidos[$parada - 1] ?? null;
                    if ($id_pedido) {
                        $query_parada = "INSERT INTO rutas_pedidos (id_ruta, id_pedido, orden_parada) VALUES ($id_ruta, $id_pedido, $orden)";
                        $mysqli->query($query_parada);
                    }
                }
            }

            // Redirigir si la distancia total está presente
            if (!empty($distancia_total)) {
                header("Location: ruta_ver2.php?id_ruta=$id_ruta");
                exit();
            } else {
                echo "Error: No se recibió la distancia total de la API.";
            }
        } else {
            echo "Error en el cálculo de la ruta: " . $response['error'];
        }
    }
}

// Función para llamar a la API de optimización de rutas
function llamar_api_optimizar_ruta($data) {
    $url = 'http://127.0.0.1:5000/optimizar_ruta';  // URL de la API en Python

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    return json_decode($result, true);
}

?>

<div class="container relative overflow shadow-md rounded-lg bg-white border-gray-300">
    <div class="flex justify-between items-center p-5">
        <div class="text-xl font-semibold text-gray-900">
            Crear una Nueva Ruta Optimizada
            <p class="text-gray-500 mt-1 text-sm font-normal">Seleccione los pedidos que desea incluir en la ruta y luego haga clic en el botón para calcular la ruta.</p>
        </div>
        
        <!-- Botón para crear ruta automática dentro del encabezado -->
        <button type="submit" form="ruta-form" name="calculo_automatico" class="text-sm border border-indigo-800 hover:bg-indigo-800 hover:text-white px-4 py-2 rounded-lg transition duration-300 flex items-center">
            <span class="material-icons text-base mr-2">add</span>
            Crear Ruta Automática
        </button>
    </div>

    <!-- El formulario comienza aquí -->
    <form id="ruta-form" method="POST" action="" class="p-6 pt-0 rounded-lg shadow-md">
        <div class="p-1 rounded-lg">
            <h2 class="text-lg font-semibold mb-2 text-gray-800">Pedidos Pendientes</h2>
            
            <!-- Caja contenedora de pedidos -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <?php while ($pedido = $result->fetch_assoc()): ?>
                    <!-- Caja individual de pedido -->
                    <div class="border border-gray-300 bg-gray-50 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 flex items-start">
                        <input type="checkbox" name="pedidos[]" value="<?php echo $pedido['id']; ?>" class="mr-3 mt-1">
                        <div>
                            <p class="font-semibold text-gray-800">Pedido ID: <?php echo $pedido['id']; ?></p>
                            <p class="text-sm text-gray-600">Latitud: <?php echo $pedido['latitud']; ?>, Longitud: <?php echo $pedido['longitud']; ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Botón para calcular ruta manual, que se mantiene debajo de los pedidos -->
        <div class="flex justify-between mt-6">
            <button type="submit" name="calcular_manual" class="bg-blue-600 text-white px-4 py-2 text-sm rounded-lg hover:bg-blue-700 transition duration-300">Calcular Ruta Manual</button>
        </div>
    </form>
</div>

<?php ob_end_flush(); // Libera el búfer de salida ?>