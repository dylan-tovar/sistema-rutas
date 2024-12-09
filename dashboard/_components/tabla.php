<?php
$pedidos = [
   ['id' => 1, 'cliente' => 'Juan', 'direccion' => 'Calle 123', 'estado' => 'Pendiente'],
   ['id' => 2, 'cliente' => 'Maria', 'direccion' => 'Avenida 456', 'estado' => 'En camino'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold text-center">Lista de Pedidos</h1>
        <table class="min-w-full bg-white mt-6 shadow-md rounded-lg">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-2 px-4">ID</th>
                    <th class="py-2 px-4">Cliente</th>
                    <th class="py-2 px-4">Dirección</th>
                    <th class="py-2 px-4">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                <tr class="border-b">
                    <td class="py-2 px-4"><?php echo $pedido['id']; ?></td>
                    <td class="py-2 px-4"><?php echo $pedido['cliente']; ?></td>
                    <td class="py-2 px-4"><?php echo $pedido['direccion']; ?></td>
                    <td class="py-2 px-4"><?php echo $pedido['estado']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
