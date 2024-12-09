<?php
    session_start();
    require '../../db/conexion.php';

    if (!isset($_SESSION['id_usuario'])) {
        header("Location: index.php");
        exit;
    }

    // Obtener el valor de "key" para determinar si es usuarios o vehículos
    $key = isset($_GET['key']) ? $_GET['key'] : '';

    // Título de la gestión según el valor de "key"
    if ($key == 'usuarios') {
        $titulo = 'Gestión de Usuarios';
    } elseif ($key == 'vehiculos') {
        $titulo = 'Gestión de Vehículos';
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
    <script defer src="https://unpkg.com/alpinejs@3.2.2/dist/cdn.min.js"></script> <!-- Incluyendo Alpine.js -->
</head>
<body class="bg-gray-100">

    <section class="flex">
        <?php include '_components/header&aside.php'; ?>
    </section>

    <!-- Container -->
    <div class="ml-64 mt-20 p-4">
        

        <?php
            if ($key == 'usuarios') {
                include 'usuario_crud.php';  
            } elseif ($key == 'vehiculos') {
                include 'vehiculo_crud.php';  
            }
        ?>
    </div>

</body>
</html>