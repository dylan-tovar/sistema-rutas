<div class="container m-0 p-0" x-data="{ openAddModal: false, openEditModal: false, user: { id: '', id_tipo: '' } }">

    <!-- Contenedor de la tabla -->
    <div class="relative overflow-x-auto shadow-md rounded-lg border border-gray-200">

        <div class="flex justify-between items-center p-5 bg-white">
            <div class="text-xl font-semibold text-gray-900">
            <?php echo $titulo; ?>
                <p class="mt-1 text-sm font-normal text-gray-500">Aquí puedes ver y gestionar la lista de usuarios registrados en el sistema.</p>
            </div>

            <button @click="openAddModal = true" class="text-sm border border-blue-800 text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition duration-300 flex items-center">
        <span class="material-icons text-base mr-2">add</span>
        Agregar Usuario
        </button>
        </div>

        <!-- Tabla de usuarios -->
        <table class="w-full text-sm text-left rtl:text-right text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3">Nombre</th>
                    <th scope="col" class="px-6 py-3">Usuario</th>
                    <th scope="col" class="px-6 py-3">Correo</th>
                    <th scope="col" class="px-6 py-3">Rol</th>
                    <th scope="col" class="px-6 py-3">
                        <span class="sr-only">Acciones</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta para obtener todos los usuarios
                $sql = "SELECT u.id, u.nombre, u.usuario, u.correo, t.tipo AS rol, u.id_tipo 
                        FROM usuarios u 
                        JOIN tipo_usuario t ON u.id_tipo = t.id";
                $result = $mysqli->query($sql);

                // Bucle para mostrar cada usuario en la tabla
                while ($row = $result->fetch_assoc()) {
                    echo "<tr class='bg-white border-b'>";
                    echo "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap'>{$row['nombre']}</td>";
                    echo "<td class='px-6 py-4'>{$row['usuario']}</td>";
                    echo "<td class='px-6 py-4'>{$row['correo']}</td>";
                    echo "<td class='px-6 py-4'>{$row['rol']}</td>";
                    echo "<td class='px-6 py-4 text-right'>
                            <button @click=\"openEditModal = true; user.id = '{$row['id']}'; user.id_tipo = '{$row['id_tipo']}';\" class='font-medium text-blue-600 dark:text-blue-500 hover:underline'>Editar Rol</button>
                            <button class='ml-4 font-medium text-red-600  hover:underline'>
                                <a href='usuario_eliminar.php?id={$row['id']}' >Eliminar</a>
                            </button>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

     <!-- Modal para agregar un usuario -->
     <div x-show="openAddModal" class="fixed z-10 inset-0 overflow-y-auto flex items-center justify-center" style="display: none;">
        <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 w-full max-w-lg">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Agregar Usuario</h2>
            <form action="usuario_guardar.php" method="POST">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" name="nombre" required class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring focus:border-blue-300">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                    <input type="text" name="usuario" required class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring focus:border-blue-300">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                    <input type="email" name="correo" required class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring focus:border-blue-300">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" name="password" required class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring focus:border-blue-300">
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">Guardar</button>
                    <button type="button" @click="openAddModal = false" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para editar usuario -->
    <div x-show="openEditModal" class="fixed z-10 inset-0 overflow-y-auto flex items-center justify-center" style="display: none;">
        <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 w-full max-w-lg">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Editar Rol de Usuario</h2>
            <form action="usuario_editar.php" method="POST">
                <input type="hidden" name="id" x-model="user.id">
                
                <!-- Selección de Rol -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                    <select name="id_tipo" x-model="user.id_tipo" class="border border-gray-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring focus:border-blue-300" required>
                        <option value="1">Administrador</option>
                        <option value="2">Usuario</option>
                        <option value="3">Repartidor</option>
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