<?php
require '../../db/conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM vehiculos WHERE id = '$id'";

    if ($mysqli->query($sql) === TRUE) {
        header("Location: gestion.php?key=vehiculos");
    } else {
        echo "Error al eliminar el vehículo: " . $mysqli->error;
    }
}
?>