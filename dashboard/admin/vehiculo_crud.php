<div class="container m-0 p-0" x-data="{ openAddModal: false, openEditModal: false, vehicle: { id: '', id_usuario_asignado: '', estado: 'disponible' } }">

    <!-- Contenedor de la tabla -->
    <div class="relative overflow-x-auto shadow-md rounded-lg border border-gray-200">

        <div class="flex justify-between items-center p-5 bg-white">
            <div class="text-xl font-semibold text-gray-900">
                Gestión de Vehículos
                <p class="mt-1 text-sm font-normal text-gray-500">Aquí puedes gestionar los vehículos del sistema.</p>
            </div>

            <button @click="openAddModal = true" class="text-sm border border-blue-800 text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition duration-300 flex items-center">
                <span class="material-icons text-base mr-2">add</span>
                Agregar Vehículo
            </button>
        </div>

        <!-- Tabla de vehículos -->
        <table class="w-full text-sm text-left rtl:text-right text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3">Placa</th>
                    <th scope="col" class="px-6 py-3">Modelo</th>
                    <th scope="col" class="px-6 py-3">Marca</th>
                    <th scope="col" class="px-6 py-3">Estado</th>
                    <th scope="col" class="px-6 py-3">Usuario Asignado</th>
                    <th scope="col" class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta para obtener todos los vehículos
                $sql = "SELECT v.id, v.placa, v.modelo, v.marca, v.estado, u.nombre AS usuario_asignado
                        FROM vehiculos v
                        LEFT JOIN usuarios u ON v.id_usuario_asignado = u.id";
                $result = $mysqli->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr class='bg-white border-b'>";
                    echo "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap'>{$row['placa']}</td>";
                    echo "<td class='px-6 py-4'>{$row['modelo']}</td>";
                    echo "<td class='px-6 py-4'>{$row['marca']}</td>";
                    echo "<td class='px-6 py-4'>{$row['estado']}</td>";
                    echo "<td class='px-6 py-4'>" . ($row['usuario_asignado'] ?? 'No asignado') . "</td>";
                    echo "<td class='px-6 py-4 text-right'>
                            <button @click=\"openEditModal = true; vehicle.id = '{$row['id']}';\" class='font-medium text-blue-600 hover:underline'>Editar</button>
                            <button class='ml-4 font-medium text-red-600 hover:underline'>
                                <a href='vehiculo_eliminar.php?id={$row['id']}' >Eliminar</a>
                            </button>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para agregar vehículo -->
    <div x-show="openAddModal" class="fixed z-10 inset-0 overflow-y-auto flex items-center justify-center" style="display: none;">
        <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 w-full max-w-lg">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Agregar Vehículo</h2>
            <form action="vehiculo_guardar.php" method="POST">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Placa</label>
                    <input type="text" name="placa" required class="border border-gray-300 rounded-lg px-3 py-2 w-full">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                    <input type="text" name="modelo" required class="border border-gray-300 rounded-lg px-3 py-2 w-full">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                    <input type="text" name="marca" required class="border border-gray-300 rounded-lg px-3 py-2 w-full">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" required class="border border-gray-300 rounded-lg px-3 py-2 w-full">
                        <option value="disponible">Disponible</option>
                        <option value="en uso">En Uso</option>
                        <option value="no disponible">No Disponible</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asignar a usuario</label>
                    <select name="id_usuario_asignado" class="border border-gray-300 rounded-lg px-3 py-2 w-full">
                        <option value="">No asignado</option>
                        <?php
                        // Seleccionar usuarios con rol de Repartidor
                        $sql = "SELECT id, nombre FROM usuarios WHERE id_tipo = 3";
                        $usuarios = $mysqli->query($sql);
                        while ($user = $usuarios->fetch_assoc()) {
                            echo "<option value='{$user['id']}'>{$user['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">Guardar</button>
                    <button type="button" @click="openAddModal = false" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para editar vehículo -->
    <div x-show="openEditModal" class="fixed z-10 inset-0 overflow-y-auto flex items-center justify-center" style="display: none;">
        <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 w-full max-w-lg">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Editar Estado de Vehículo</h2>
            <form action="vehiculo_editar.php" method="POST">
                <input type="hidden" name="id" x-model="vehicle.id">
                        
                <!-- Selección de Estado -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select name="estado" x-model="vehicle.estado" class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring focus:border-blue-300" required>
                        <option value="disponible">Disponible</option>
                        <option value="en uso">En Uso</option>
                        <option value="no disponible">No Disponible</option>
                    </select>
                </div>
                        
                <!-- Asignar a usuario -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asignar a usuario</label>
                    <select name="id_usuario_asignado" x-model="vehicle.id_usuario_asignado" class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring focus:border-blue-300">
                        <option value="">No asignado</option>
                        <?php
                        // Seleccionar usuarios con rol de Repartidor
                        $sql = "SELECT id, nombre FROM usuarios WHERE id_tipo = 3";
                        $usuarios = $mysqli->query($sql);
                        while ($user = $usuarios->fetch_assoc()) {
                            echo "<option value='{$user['id']}'>{$user['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                    
                <div class="flex justify-end space-x-4">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">Guardar</button>
                    <button type="button" @click="openEditModal = false" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>