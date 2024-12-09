<?php
require '../../db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $estado = $_POST['estado'];
    $id_usuario_asignado = !empty($_POST['id_usuario_asignado']) ? $_POST['id_usuario_asignado'] : NULL;

    $sql = "UPDATE vehiculos SET estado = ?, id_usuario_asignado = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssi', $estado, $id_usuario_asignado, $id);

    if ($stmt->execute()) {
        header("Location: gestion.php?key=vehiculos");
    } else {
        echo "Error al actualizar el vehículo: " . $mysqli->error;
    }
    $stmt->close();
}
?>