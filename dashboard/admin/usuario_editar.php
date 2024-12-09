<?php
require '../../db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $id_tipo = $_POST['id_tipo'];

    // Consulta para actualizar el rol del usuario
    $sql = "UPDATE usuarios SET id_tipo = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ii', $id_tipo, $id);

    if ($stmt->execute()) {
        header("Location: gestion.php?key=usuarios");
    } else {
        echo "Error al actualizar el rol del usuario";
    }
    $stmt->close();
}
?>