<?php
require '../../db/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $correo = $_POST['correo'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 

    $sql = "INSERT INTO usuarios (nombre, usuario, correo, password, last_session, id_tipo) 
            VALUES ('$nombre', '$usuario', '$correo', '$password', NOW(), 2)";
    
    if ($mysqli->query($sql) === TRUE) {
        header("Location: gestion.php?key=usuarios");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $mysqli->error;
    }
}
?>