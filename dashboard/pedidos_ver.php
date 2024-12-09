<?php
    $sql = "SELECT p.id, p.fecha_pedido, p.estado, d.nombre_direccion, d.latitud, d.longitud 
            FROM pedidos p
            JOIN direcciones d ON p.id_direccion = d.id
            WHERE p.id_cliente = ? AND p.estado = 'pendiente'";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<div class="relative overflow-x-auto shadow-md rounded-lg border border-gray-200">
    <div class="flex justify-between items-center p-5 bg-white">
        <div class="text-xl font-semibold text-gray-900">
            Gestión de Pedidos
            <p class="mt-1 text-sm font-normal text-gray-500">Aquí puedes ver y gestionar tus pedidos pendientes.</p>
        </div>
        <a href="pedidos.php?key=crear" class="text-sm border border-blue-800 text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition duration-300 flex items-center">
            <span class="material-icons text-base mr-2">add</span>
            Agregar Pedido
        </a>
    </div>

    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
            <tr>
                <th class="px-6 py-3">Nombre de la Dirección</th>
                <th class="px-6 py-3">Fecha del Pedido</th>
                <th class="px-6 py-3">Estado</th>
                <th class="px-6 py-3">Latitud</th>
                <th class="px-6 py-3">Longitud</th>
                <th class="px-6 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="bg-white border-b">
                    <td class="px-6 py-4"><?php echo $row['nombre_direccion']; ?></td>
                    <td class="px-6 py-4"><?php echo $row['fecha_pedido']; ?></td>
                    <td class="px-6 py-4"><?php echo ucfirst($row['estado']); ?></td>
                    <td class="px-6 py-4"><?php echo $row['latitud']; ?></td>
                    <td class="px-6 py-4"><?php echo $row['longitud']; ?></td>
                    <td class="px-6 py-4 text-right">
                        <?php if ($row['estado'] === 'pendiente'): ?>
                            <a href="pedidos_cancelar.php?id=<?php echo $row['id']; ?>" class="text-red-600 hover:underline">Cancelar</a>
                        <?php else: ?>
                            <span class="text-gray-500">Sin acciones</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>