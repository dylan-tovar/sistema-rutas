<?php
    $sql = "SELECT * FROM direcciones WHERE id_cliente = ? AND activa = 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<div class="relative overflow-x-auto shadow-md rounded-lg border border-gray-200">
    <div class="flex justify-between items-center p-5 bg-white">
        <div class="text-xl font-semibold text-gray-900">
            Gestión de Direcciones
            <p class="mt-1 text-sm font-normal text-gray-500">Aquí puedes ver y gestionar tus direcciones registradas.</p>
        </div>
        <a href="direccion.php?key=crear" class="text-sm border border-blue-800 text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition duration-300 flex items-center">
            <span class="material-icons text-base mr-2">add</span>
            Agregar Dirección
        </a>
    </div>

    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
            <tr>
                <th class="px-6 py-3">Nombre de la Dirección</th>
                <th class="px-6 py-3">Dirección Aproximada</th>
                <th class="px-6 py-3">Latitud</th>
                <th class="px-6 py-3">Longitud</th>
                <th class="px-6 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="bg-white border-b">
                    <td class="px-6 py-4"><?php echo $row['nombre_direccion']; ?></td>
                    <td class="px-6 py-4"><?php echo $row['direccion_aprox']; ?></td>
                    <td class="px-6 py-4"><?php echo $row['latitud']; ?></td>
                    <td class="px-6 py-4"><?php echo $row['longitud']; ?></td>
                    <td class="px-6 py-4 text-right">
                        <a href="direccion_eliminar.php?id=<?php echo $row['id']; ?>" class="text-red-600 hover:underline">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>