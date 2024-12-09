<?php
session_start();
require '../db/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

// Recibir datos del formulario
$idCliente = $_SESSION['id_usuario'];
$nombreDireccion = $_POST['nombre_direccion'];
$direccionAprox = $_POST['direccion_aprox'];
$latitud = $_POST['latitud'];
$longitud = $_POST['longitud'];

// Insertar la nueva dirección en la tabla `direcciones`
$sqlDireccion = "INSERT INTO direcciones (nombre_direccion, direccion_aprox, latitud, longitud, id_cliente) VALUES (?, ?, ?, ?, ?)";
$stmtDireccion = $mysqli->prepare($sqlDireccion);
$stmtDireccion->bind_param("ssddi", $nombreDireccion, $direccionAprox, $latitud, $longitud, $idCliente);

if ($stmtDireccion->execute()) {
    header("Location: direccion.php?key=ver&msg=Dirección agregada correctamente");
} else {
    echo "Error al agregar la dirección: " . $mysqli->error;
}

$stmtDireccion->close();
$mysqli->close();
?>