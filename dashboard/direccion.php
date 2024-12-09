<?php
session_start();
require '../db/conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

// Obtener el valor de "key" para determinar la acción
$key = isset($_GET['key']) ? $_GET['key'] : '';

// Título de la gestión
if ($key == 'ver') {
    $titulo = 'Gestión de Direcciones';
} elseif ($key == 'crear') {
    $titulo = 'Agregar Nueva Dirección';
} else {
    die('Error: Tipo de gestión no especificado');
}

$idUsuario = $_SESSION['id_usuario'];
$sql = "SELECT * FROM usuarios WHERE id = '$idUsuario'";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.2.2/dist/cdn.min.js"></script>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }

        .autocomplete-suggestions {
            border: 1px solid #ddd;
            background: #fff;
            max-height: 200px;
            overflow-y: auto;
            position: absolute;
            z-index: 1000;
            width: 100%;
            margin-top: 2px;
        }

        .autocomplete-suggestion {
            padding: 10px;
            cursor: pointer;
            color: black;
        }

        .autocomplete-suggestion:hover {
            background: #f0f0f0;
        }
    </style>
</head>
<body class="bg-gray-100">
    <section class="flex">
        <?php include '_components/header&aside.php'; ?>
    </section>

    <!-- Container -->
    <div class="ml-64 mt-20 p-4">
        <?php
            if ($key == 'ver') {
                include 'direccion_ver.php';  
            } elseif ($key == 'crear') {
                include 'direccion_agregar.php';  
            }
        ?>
    </div>
</body>
</html>