<?php
// Incluir la conexión a la base de datos
include 'db/conexion.php';

// Manejar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener todos los pedidos pendientes de la base de datos
$query = "SELECT id, latitud, longitud FROM pedidos WHERE estado = 'pendiente'";
$result = $mysqli->query($query);

// Definir el depot (puedes cambiar las coordenadas según tu necesidad)
$depot = [
    'lat' => 10.500000,  // Coordenada de latitud del depot
    'lon' => -66.916664  // Coordenada de longitud del depot
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
                    $id_pedido = $ids_pedidos[$parada - 1] ?? null;  // Ajustar el índice
                    if ($id_pedido) {
                        $query_parada = "INSERT INTO rutas_pedidos (id_ruta, id_pedido, orden_parada) VALUES ($id_ruta, $id_pedido, $orden)";
                        $mysqli->query($query_parada);
                    }
                }
            }

            // Verificar si la distancia total está presente en la respuesta
            if (!empty($distancia_total)) {
                // Redirigir a mostrar_ruta.php con la ID de la ruta y la distancia total
                header("Location: mostrar_ruta.php?id_ruta=$id_ruta&distancia_total=" . urlencode($distancia_total));
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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optimizar Ruta</title>
</head>
<body>
    <h1>Optimizar Ruta</h1>

    <form method="POST" action="">
        <h2>Pedidos Pendientes</h2>
        <?php while ($pedido = $result->fetch_assoc()): ?>
            <input type="checkbox" name="pedidos[]" value="<?php echo $pedido['id']; ?>">
            Pedido ID: <?php echo $pedido['id']; ?> (Latitud: <?php echo $pedido['latitud']; ?>, Longitud: <?php echo $pedido['longitud']; ?>)<br>
        <?php endwhile; ?>

        <br><br>
        <button type="submit" name="calcular_manual">Calcular Ruta Manual</button>
        <button type="submit" name="calculo_automatico">Calcular Ruta Automáticamente</button>
    </form>
</body>
</html>