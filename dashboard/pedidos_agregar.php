<?php
    $sql = "SELECT id, nombre_direccion FROM direcciones WHERE id_cliente = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<div class="relative overflow-x-auto shadow-md rounded-lg border border-gray-200">
    <div class="flex justify-between items-center px-6 pt-5 bg-white">
        <div class="text-xl font-semibold text-gray-900">
            Registro de Pedidos
            <p class="mt-1 text-sm font-normal text-gray-500">En esta sección puedes registrar nuevos pedidos</p>
        </div>
    </div>

    <form id="form-pedido" method="POST" action="pedidos_guardar.php" class="bg-white p-6 pb-5 rounded-lg shadow-md">
        <div class="mb-2">
            <label for="id_direccion" class="block text-gray-700 font-medium mb-1">Seleccionar Dirección:</label>
            <select id="id_direccion" name="id_direccion" required class="border border-gray-300 rounded-lg px-4 py-2 w-full focus:outline-none focus:ring focus:border-blue-300">
                <option value="">Selecciona una dirección</option>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre_direccion']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <input type="hidden" id="id_cliente" name="id_cliente" value="<?php echo $idUsuario; ?>"> 

        <button type="submit" class="text-sm border border-blue-800 text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition duration-300 flex items-center">
            <span class="material-icons text-base mr-2">add</span>
            Agregar Pedido
        </button>
    </form>
</div>