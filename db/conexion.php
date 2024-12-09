<?php 

    $mysqli = new mysqli('localhost', 'root', '', 'rutas');

    if(mysqli_connect_errno()){
        echo 'Error al conectar a la base de datos';
        exit();
    }