<?php
require '../../db/conexion.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM usuarios WHERE id='$id'";

    if ($mysqli->query($sql) === TRUE) {
        header("Location: gestion.php?key=usuarios");
    } else {
        echo "Error al eliminar el usuario: " . $mysqli->error;
    }
}
?>