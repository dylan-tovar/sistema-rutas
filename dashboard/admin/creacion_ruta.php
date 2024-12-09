<?php
// Conexión a la base de datos
include '../../db/conexion.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir manualmente el depot
$depot = [
    "lat" => 10.500000, // Cambia a las coordenadas reales del depot
    "lon" => -66.916664, // Cambia a las coordenadas reales del depot
    "direccion" => "Depot Central",
    "usuario" => "Sistema"
];

// Función para guardar la ruta en la base de datos
function guardarRuta($mysqli, $pedidos_seleccionados, $vehiculos_utilizados, $rutas) {
    // Insertar la ruta en la tabla 'rutas' con una consulta preparada
    $query_insert_ruta = "INSERT INTO rutas (fecha_creacion, vehiculos_utilizados, estado) VALUES (NOW(), ?, 'pendiente')";
    $stmt = $mysqli->prepare($query_insert_ruta);
    $stmt->bind_param("i", $vehiculos_utilizados);
    if (!$stmt->execute()) {
        echo "Error al guardar la ruta: " . $stmt->error;
        return false;
    }

    // Obtener el ID de la ruta creada
    $id_ruta = $mysqli->insert_id;

    // Insertar los pedidos y su orden en la tabla 'rutas_pedidos'
    $query_insert_ruta_pedido = "INSERT INTO rutas_pedidos (id_ruta, id_pedido, orden_parada) VALUES (?, ?, ?)";
    $stmt_pedido = $mysqli->prepare($query_insert_ruta_pedido);

    foreach ($rutas as $vehiculo => $ruta) {
        $orden = 1; // Para manejar el orden de las paradas en la ruta
        foreach ($ruta as $index => $id_pedido) {
            if (isset($pedidos_seleccionados[$id_pedido])) { // Evitar índice indefinido
                $pedido_id = $pedidos_seleccionados[$id_pedido];
                $stmt_pedido->bind_param("iii", $id_ruta, $pedido_id, $orden);
                if (!$stmt_pedido->execute()) {
                    echo "Error al guardar los pedidos de la ruta: " . $stmt_pedido->error;
                    return false;
                }
                $orden++;
            }
        }
    }

    return $id_ruta; // Retornar el ID de la ruta
}

// Función para generar la ruta automáticamente
function generarRutaAutomatica($mysqli, $depot) {
    // Consultar las direcciones y los usuarios desde la base de datos
    $query = "SELECT p.id, p.latitud, p.longitud, d.nombre_direccion AS direccion, u.nombre AS usuario
              FROM pedidos p
              JOIN direcciones d ON p.id_direccion = d.id
              JOIN usuarios u ON p.id_cliente = u.id
              WHERE p.estado = 'pendiente'";
    $result = $mysqli->query($query);

    if (!$result) {
        echo "Error al consultar pedidos: " . $mysqli->error;
        return false;
    }

    $direcciones = [$depot]; // Comenzamos con el depot como el primer punto
    $pedidos_seleccionados = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $direcciones[] = [
                "lat" => floatval($row['latitud']),
                "lon" => floatval($row['longitud']),
                "direccion" => $row['direccion'],
                "usuario" => $row['usuario']
            ];
            $pedidos_seleccionados[] = $row['id'];
        }
    } else {
        echo "<div class='alert alert-warning'>No hay pedidos pendientes para generar la ruta.</div>";
        return false;
    }

    // Consultar la cantidad de vehículos disponibles
    $query_vehiculos = "SELECT COUNT(*) AS total_vehiculos FROM vehiculos WHERE estado = 'disponible'";
    $result_vehiculos = $mysqli->query($query_vehiculos);
    $vehiculos_disponibles = 1; // Valor por defecto
    if ($result_vehiculos) {
        $row = $result_vehiculos->fetch_assoc();
        $vehiculos_disponibles = intval($row['total_vehiculos']);
    }

    // Asegurarse de que hay al menos dos direcciones (incluyendo el depot)
    if (count($direcciones) < 3) {
        echo "<div class='alert alert-danger'>Se requieren al menos dos direcciones para optimizar una ruta.</div>";
        return false;
    }

    // URL de la API para optimizar la ruta
    $urlApiOptimizarRuta = 'http://127.0.0.1:5000/optimizar_ruta';
    $data = [
        "direcciones" => $direcciones,
        "vehiculos_disponibles" => $vehiculos_disponibles
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

    if (curl_errno($ch)) {
        echo "<div class='alert alert-danger'>Error en la conexión: " . curl_error($ch) . "</div>";
        return false;
    }

    $result = json_decode($response, true);

    // Validar que haya una ruta en la respuesta
    if (!isset($result['rutas'])) {
        echo "<div class='alert alert-danger'>No se pudo calcular la ruta optimizada.</div>";
        return false;
    }

    // Guardar la ruta y los pedidos en la base de datos
    $id_ruta = guardarRuta($mysqli, $pedidos_seleccionados, $vehiculos_disponibles, $result['rutas']);

    // Actualizar el estado de los pedidos como 'en ruta'
    $query_update = "UPDATE pedidos SET estado = 'en_proceso' WHERE estado = 'pendiente'"; 
    $mysqli->query($query_update);
    
    return $id_ruta; // Retornar el ID de la ruta generada
}

// Función para crear la ruta manualmente
function crearRutaManual($mysqli, $depot, $pedidos_seleccionados) {
    $pedidos_ids = implode(',', array_map('intval', $pedidos_seleccionados));
    $query = "SELECT p.id, p.latitud, p.longitud, d.nombre_direccion AS direccion, u.nombre AS usuario
              FROM pedidos p
              JOIN direcciones d ON p.id_direccion = d.id
              JOIN usuarios u ON p.id_cliente = u.id
              WHERE p.id IN ($pedidos_ids)";
    $result = $mysqli->query($query);

    if (!$result) {
        echo "Error al consultar pedidos seleccionados: " . $mysqli->error;
        return false;
    }

    $direcciones = [$depot]; // Comenzamos con el depot como el primer punto
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $direcciones[] = [
                "lat" => floatval($row['latitud']),
                "lon" => floatval($row['longitud']),
                "direccion" => $row['direccion'],
                "usuario" => $row['usuario']
            ];
        }
    }

    // Consultar la cantidad de vehículos disponibles
    $query_vehiculos = "SELECT COUNT(*) AS total_vehiculos FROM vehiculos WHERE estado = 'disponible'";
    $result_vehiculos = $mysqli->query($query_vehiculos);
    $vehiculos_disponibles = 1; // Valor por defecto
    if ($result_vehiculos) {
        $row = $result_vehiculos->fetch_assoc();
        $vehiculos_disponibles = intval($row['total_vehiculos']);
    }

    // Asegurarse de que hay al menos dos direcciones (incluyendo el depot)
    if (count($direcciones) < 3) {
        echo "<div class='alert alert-danger'>Se requieren al menos dos direcciones para optimizar una ruta.</div>";
        return false;
    }

    // URL de la API para optimizar la ruta
    $urlApiOptimizarRuta = 'http://127.0.0.1:5000/optimizar_ruta';
    $data = [
        "direcciones" => $direcciones,
        "vehiculos_disponibles" => $vehiculos_disponibles
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

    if (curl_errno($ch)) {
        echo "<div class='alert alert-danger'>Error en la conexión: " . curl_error($ch) . "</div>";
        return false;
    }

    $result = json_decode($response, true);

    // Validar que haya una ruta en la respuesta
    if (!isset($result['rutas'])) {
        echo "<div class='alert alert-danger'>No se pudo calcular la ruta optimizada.</div>";
        return false;
    }

    // Guardar la ruta y los pedidos en la base de datos
    $id_ruta = guardarRuta($mysqli, $pedidos_seleccionados, $vehiculos_disponibles, $result['rutas']);

    // Actualizar el estado de los pedidos seleccionados como 'en ruta'
    $query_update = "UPDATE pedidos SET estado = 'en ruta' WHERE id IN ($pedidos_ids)";
    $mysqli->query($query_update);
    
    return $id_ruta; // Retornar el ID de la ruta creada
}

// Procesar la solicitud dependiendo de la acción
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['generar_automaticamente'])) {
        $id_ruta = generarRutaAutomatica($mysqli, $depot);
        if ($id_ruta) {
            header("Location: ver_ruta.php?id_ruta=$id_ruta");
            exit;
        }
    } elseif (isset($_POST['crear_ruta_manual']) && !empty($_POST['pedidos'])) {
        $pedidos_seleccionados = $_POST['pedidos'];
        $id_ruta = crearRutaManual($mysqli, $depot, $pedidos_seleccionados);
        if ($id_ruta) {
            header("Location: ver_ruta.php?id_ruta=$id_ruta");
            exit;
        }
    }
}
?>

<div class="container mx-auto">
    <h2 class="text-2xl font-bold mb-4">Seleccionar Pedidos para la Ruta</h2>

    <form method="POST" action="">
        <div class="grid grid-cols-1 gap-4">
            <?php
            // Obtener pedidos pendientes
            $query_pedidos = "SELECT p.id, d.nombre_direccion, u.nombre 
                              FROM pedidos p 
                              JOIN direcciones d ON p.id_direccion = d.id 
                              JOIN usuarios u ON p.id_cliente = u.id 
                              WHERE p.estado = 'pendiente'";
            $result_pedidos = $mysqli->query($query_pedidos);

            if ($result_pedidos->num_rows > 0) {
                while ($row = $result_pedidos->fetch_assoc()) {
                    echo "<label><input type='checkbox' name='pedidos[]' value='{$row['id']}'> 
                          {$row['nombre_direccion']} (Cliente: {$row['nombre']})</label>";
                }
            } else {
                echo "<p>No hay pedidos pendientes.</p>";
            }
            ?>
        </div>

        <div class="mt-4">
            <button type="submit" name="crear_ruta_manual" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">
                Crear Ruta Manualmente
            </button>
            <button type="submit" name="generar_automaticamente" class="bg-gray-500 text-white px-4 py-2 rounded">
                Generar Automáticamente
            </button>
        </div>
    </form>
</div>