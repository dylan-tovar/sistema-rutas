<?php
    session_start();
    require '../db/conexion.php';

    if (!isset($_SESSION['id_usuario'])) {
        header("Location: index.php");
        exit;
    }

    $key = isset($_GET['key']) ? $_GET['key'] : '';
    if ($key == 'ver') {
        $titulo = 'Gestión de Pedidos';
    } elseif ($key == 'crear') {
        $titulo = 'Agregar Nuevo Pedido';
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
    <script defer src="https://unpkg.com/alpinejs@3.2.2/dist/cdn.min.js"></script> 
</head>
<body class="bg-gray-100">

    <section class="flex">
        <?php include '_components/header&aside.php'; ?>
    </section>

    <div class="ml-64 mt-20 p-4">
        <?php
            if ($key == 'ver') {
                include 'pedidos_ver.php';  
            } elseif ($key == 'crear') {
                include 'pedidos_agregar.php';  
            }
        ?>
    </div>
</body>
</html>