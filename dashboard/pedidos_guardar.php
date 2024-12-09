<?php
// Conexión a la base de datos
include '../db/conexion.php';

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtén el cliente y dirección del formulario
    $idCliente = $_SESSION['id_usuario'];
    $idDireccion = $_POST['id_direccion'];

    // Validar que la dirección exista y obtener latitud y longitud
    $sqlDireccion = "SELECT latitud, longitud FROM direcciones WHERE id = ? AND id_cliente = ?";
    $stmtDireccion = $mysqli->prepare($sqlDireccion);
    $stmtDireccion->bind_param("ii", $idDireccion, $idCliente);
    $stmtDireccion->execute();
    $resultDireccion = $stmtDireccion->get_result();
    
    if ($resultDireccion->num_rows === 0) {
        echo "Error: Dirección no encontrada.";
        exit;
    }

    $direccionData = $resultDireccion->fetch_assoc();
    $latitud = $direccionData['latitud'];
    $longitud = $direccionData['longitud'];

    // Insertar el pedido en la tabla `pedidos`
    $sqlInsert = "INSERT INTO pedidos (id_cliente, id_direccion, latitud, longitud) VALUES (?, ?, ?, ?)";
    $stmtInsert = $mysqli->prepare($sqlInsert);
    $stmtInsert->bind_param("iidd", $idCliente, $idDireccion, $latitud, $longitud);

    if ($stmtInsert->execute()) {
        header("Location: pedidos.php?key=ver");
        exit;
    } else {
        echo "Error al guardar el pedido.";
    }
} else {
    echo "Método de solicitud no válido.";
}
?>