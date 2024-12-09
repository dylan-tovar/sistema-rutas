<?php
require '../../db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $placa = $_POST['placa'];
    $modelo = $_POST['modelo'];
    $marca = $_POST['marca'];
    $estado = $_POST['estado'];
    $id_usuario_asignado = !empty($_POST['id_usuario_asignado']) ? $_POST['id_usuario_asignado'] : NULL;

    $sql = "INSERT INTO vehiculos (placa, modelo, marca, estado, id_usuario_asignado) 
            VALUES ('$placa', '$modelo', '$marca', '$estado', ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $id_usuario_asignado);

    if ($stmt->execute()) {
        header("Location: gestion.php?key=vehiculos");
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }
    $stmt->close();
}
?>