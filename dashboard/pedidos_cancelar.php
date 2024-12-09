<?php
session_start();
require '../db/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

$idPedido = $_GET['id'];
$idCliente = $_SESSION['id_usuario'];

// Cambiar el estado del pedido a "cancelado"
$sql = "UPDATE pedidos SET estado = 'cancelado' WHERE id = ? AND id_cliente = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $idPedido, $idCliente);

if ($stmt->execute()) {
    header("Location: pedidos.php?key=ver&msg=Pedido cancelado correctamente");
} else {
    echo "Error al cancelar el pedido: " . $mysqli->error;
}

$stmt->close();
$mysqli->close();
?>