<?php
    session_start();
    ob_start();  // Inicia el búfer de salida para evitar problemas con headers
    require '../../db/conexion.php';

    if (!isset($_SESSION['id_usuario'])) {
        header("Location: index.php");
        exit;
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
        <?php include 'ruta_optimizar.php'; ?>
    </div>

</body>
</html>

<?php ob_end_flush();  // Libera el búfer de salida al final del script ?>