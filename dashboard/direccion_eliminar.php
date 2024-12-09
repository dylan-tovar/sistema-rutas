<?php
session_start();
require '../db/conexion.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

$idUsuario = $_SESSION['id_usuario'];
$idDireccion = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Comprobar que el ID de la dirección es válido y que pertenece al usuario
$sql = "SELECT id FROM direcciones WHERE id = ? AND id_cliente = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $idDireccion, $idUsuario);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Si existe la dirección y pertenece al usuario, marcarla como inactiva
    $stmt->close();
    $updateSQL = "UPDATE direcciones SET activa = 0 WHERE id = ?";
    $updateStmt = $mysqli->prepare($updateSQL);
    $updateStmt->bind_param("i", $idDireccion);

    if ($updateStmt->execute()) {
        // Redirigir después de marcar como inactiva
        header("Location: direccion.php?key=ver");
        exit();
    } else {
        echo "Error al desactivar la dirección.";
    }
} else {
    // Si no se encuentra la dirección o no pertenece al usuario, mostrar un mensaje de error
    echo "No se encontró la dirección o no tienes permiso para desactivarla.";
}
?>
