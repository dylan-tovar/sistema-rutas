<?php
	session_start();
	require '../../db/conexion.php';
	require '../../login/funcs/funcs.php';

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
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.2.2/dist/cdn.min.js"></script> <!-- Incluyendo Alpine.js -->
</head>
<body class="bg-gray-100">

    <!-- Section que envuelve header y aside -->
    <section class="flex">
        <!-- Incluye el header y el aside dentro del mismo contenedor -->
        <?php include '_components/header&aside.php'; ?>
    </section>

    <!-- Contenedor principal para el contenido, asegurando espacio para header y aside -->
    <div class="ml-64 mt-20 p-4">
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Metric Card 1: Rutas Optimizadas -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold text-gray-700">Rutas Optimizadas</h3>
                <p class="text-2xl font-bold text-indigo-600">120</p>
                <p class="text-sm text-gray-500">+30 optimizadas en los últimos 7 días</p>
            </div>
            
            <!-- Metric Card 2: Entregas Completadas -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold text-gray-700">Entregas Completadas</h3>
                <p class="text-2xl font-bold text-indigo-600">450</p>
                <p class="text-sm text-gray-500">+50 en los últimos 7 días</p>
            </div>
            
            <!-- Metric Card 3: Tiempo Promedio de Entrega -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold text-gray-700">Tiempo Promedio de Entrega</h3>
                <p class="text-2xl font-bold text-indigo-600">35 min</p>
                <p class="text-sm text-gray-500">-5 min en los últimos 7 días</p>
            </div>
            
            <!-- Metric Card 4: Costos Operativos Reducidos -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold text-gray-700">Costos Operativos Reducidos</h3>
                <p class="text-2xl font-bold text-indigo-600">15%</p>
                <p class="text-sm text-gray-500">-5% en el último mes</p>
            </div>
        </section>
        
        <!-- Performance Chart -->
        <section class="bg-white p-6 rounded-lg shadow-lg mt-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Eficiencia de Entregas</h3>
            <div class="h-80"> <!-- Ajustamos la altura para que sea mayor -->
                <!-- Aquí puedes insertar un gráfico de performance real -->
                <canvas id="performanceChart"></canvas>
            </div>
        </section>
    </div>

    <!-- Incluye librería para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4', 'Semana 5'],
                datasets: [{
                    label: 'Tiempo Promedio de Entrega (min)',
                    data: [40, 38, 36, 35, 33],
                    borderColor: 'rgba(0, 123, 255, 0.7)',
                    fill: false
                }, {
                    label: 'Entregas Completadas',
                    data: [200, 220, 240, 450, 470],
                    borderColor: 'rgba(255, 99, 132, 0.7)',
                    fill: false
                }, {
                    label: 'Rutas Optimizadas',
                    data: [50, 80, 100, 120, 130],
                    borderColor: 'rgba(60, 179, 113, 0.7)',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,  
                scales: {
                    x: {
                        display: true
                    },
                    y: {
                        display: true
                    }
                }
            }
        });
    </script>

</body>
</html>