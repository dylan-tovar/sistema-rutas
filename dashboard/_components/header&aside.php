    <!-- Header -->
    <header class="bg-white py-4 fixed shadow w-full">
    <div class="container mx-auto items-center flex justify-between">
        <a href="#" class="text-xl font-semibold text-gray-900 flex">
            <img src="../assets/img/logo.svg" alt="Logo" class="h-8 px-1">
            OptimizaRutas
        </a>
        <p class="text-sm text-gray-500">Conectado como: <strong><?php echo $row['nombre']; ?></strong></p>
        <p class="text-sm text-gray-500"> Último inicio de sesión: <?php echo $row['last_session']; ?></p>

        <!-- Dropdown for settings with Alpine.js -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="material-icons mr-3 text-gray-600 hover:text-gray-800 focus:outline-none">settings</button>

            <!-- Dropdown menu -->
            <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10">
                <a href="../login/logout.php" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <span class="material-icons mr-2 text-gray-500">logout</span>
                    Cerrar sesión
                </a>
            </div>
        </div>
    </div>
</header>

    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 z-20 flex flex-col w-64 h-full pt-16 font-normal duration-75 transition-width">
        <div class="relative flex flex-col flex-1 min-h-0 pt-0 bg-white border-r border-gray-200">
            <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto">
                <ul class="flex-1 px-3 space-y-1 bg-white divide-y divide-gray-200">

                    <!-- Dashboard -->
                    <li>
                        <a href="index.php" class="flex items-center p-2 text-md text-gray-900 rounded-lg hover:bg-gray-100 group">
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>

                     <!-- Registro Dropdown -->
                     <li x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100">
                            <span class="flex-1 ml-3 text-left whitespace-nowrap">Direcciones</span>
                            <span class="material-icons" :class="open ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        <ul x-show="open" class="py-2 space-y-2 pl-6">
                            <li>
                                <a href="direccion.php?key=ver" class="flex items-center p-2 text-base text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100">Mis Direcciones</a>
                            </li>
                            <li>
                                <a href="direccion.php?key=crear" class="flex items-center p-2 text-base text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100">Registra una Dirección</a>
                            </li>
                        </ul>
                    </li>
                    
                     <!-- Pedidos Dropdown -->
                     <li x-data="{ open: false }">
                        <button type="button" @click="open = !open" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100">
                            <span class="flex-1 ml-3 text-left whitespace-nowrap">Pedidos</span>
                            <span class="material-icons" :class="open ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        <ul x-show="open" class="py-2 space-y-2 pl-6">
                            <li>
                                <a href="pedidos.php?key=ver" class="flex items-center p-2 text-base text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100">Mis Pedidos</a>
                            </li>
                            <li>
                                <a href="pedidos.php?key=crear" class="flex items-center p-2 text-base text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100">Crear un Pedido</a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Estado de Entregas -->
                    <li>
                        <a href="#" class="flex items-center p-2 text-md text-gray-900 rounded-lg hover:bg-gray-100 group">
                            <span class="ml-3">Estado de Entregas</span>
                        </a>
                    </li>
                    
                    <!-- Historial de Entregas -->
                    <li>
                        <a href="#" class="flex items-center p-2 text-md text-gray-900 rounded-lg hover:bg-gray-100 group">
                            <span class="ml-3">Historial de Entregas</span>
                        </a>
                    </li>
                    
                    <!-- Facturación -->
                    <li>
                        <a href="#" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group">
                            <span class="ml-3">Facturación</span>
                        </a>
                    </li>

                </ul>
                
                <!-- GitHub Repo -->
                <!-- <div class="pt-2 space-y-2">
                    <a href="https://github.com/dylan-tovar" target="_blank" class="flex items-center p-2 text-base text-gray-900 transition duration-75 rounded-lg hover:bg-gray-100 group">
                         <img src="../assets/img/github-mark.svg" alt="github-mark" class="h-7 ml-3">
                         <img src="../assets/img/GitHub_Logo.png" alt="github-logo" class="h-7 ml-1">
                         <span class="text-md text-gray-600">Repositorio</span>
                    </a>
                </div> -->
            </div>
        </div>
    </aside>
