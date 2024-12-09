<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Optimización de Rutas - Landing Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    html {
      scroll-behavior: smooth;
    }
  </style>
</head>
<body class="bg-gray-100 text-gray-900">

  <!-- Header -->
  <header class="bg-white shadow-sm">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 py-4 flex justify-between items-center">
      <a href="#" class="text-2xl font-bold text-gray-900">
        <img src="assets/img/logo.svg" alt="Logo" class="h-8 inline-block mr-2">
        OptimizaRutas
    </a>
      <nav class="flex space-x-6">
        <a href="#como-funciona" class="px-4 text-[14px] flex items-center text-gray-500 hover:text-blue-600">Cómo Funciona</a>
        <a href="login/" class="text-gray-600 hover:text-blue-600">Login</a>
        <a href="login/r.php" class="text-gray-600 hover:text-blue-600">Sign Up</a>
      </nav>
    </div>
  </header>

  <!-- Hero -->
  <section class="overflow-hidden bg-white py-24 sm:py-32">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-2">
        <!-- Left: Texto -->
        <div class="lg:pr-8 lg:pt-4">
          <div class="lg:max-w-lg">
            <h2 class="text-base font-semibold leading-7 text-indigo-600">Optimiza tu logística</h2>
            <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Simplifica la gestión de rutas y vehículos</p>
            <p class="mt-6 text-lg leading-8 text-gray-600">
              Nuestra plataforma de optimización de rutas ayuda a reducir costos operativos, agilizar la entrega de pedidos y mejorar la eficiencia de tu flota de vehículos. Todo en un solo lugar.
            </p>
            <!-- Description List -->
            <dl class="mt-10 max-w-xl space-y-8 text-base leading-7 text-gray-600 lg:max-w-none">
              <div class="relative pl-9">
                <dt class="inline font-semibold text-gray-900">
                  <svg class="absolute left-1 top-1 h-5 w-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M5.5 17a4.5 4.5 0 0 1-1.44-8.765 4.5 4.5 0 0 1 8.302-3.046 3.5 3.5 0 0 1 4.504 4.272A4 4 0 0 1 15 17H5.5Zm3.75-2.75a.75.75 0 0 0 1.5 0V9.66l1.95 2.1a.75.75 0 1 0 1.1-1.02l-3.25-3.5a.75.75 0 0 0-1.1 0l-3.25 3.5a.75.75 0 1 0 1.1 1.02l1.95-2.1v4.59Z"/>
                  </svg>
                  Optimización Automática
                </dt>
                <dd class="inline">Genera rutas eficientes en segundos basadas en tus necesidades específicas.</dd>
              </div>

              <div class="relative pl-9">
                <dt class="inline font-semibold text-gray-900">
                  <svg class="absolute left-1 top-1 h-5 w-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Z"/>
                  </svg>
                  Gestión de Vehículos
                </dt>
                <dd class="inline">Asigna y gestiona tu flota de vehículos de manera inteligente y automática.</dd>
              </div>

              <div class="relative pl-9">
                <dt class="inline font-semibold text-gray-900">
                  <svg class="absolute left-1 top-1 h-5 w-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M4.632 3.533A2 2 0 0 1 6.577 2h6.846a2 2 0 0 1 1.945 1.533l1.976 8.234A3.489 3.489 0 0 0 16 11.5H4c-.476 0-.93.095-1.344.267l1.976-8.234Z" />
                    <path d="M4 13a2 2 0 1 0 0 4h12a2 2 0 1 0 0-4H4Z"/>
                  </svg>
                  Pedidos Centralizados
                </dt>
                <dd class="inline">Controla y organiza todos tus pedidos en una sola plataforma.</dd>
              </div>
            </dl>
          </div>
        </div>
        
        <!-- Right: Imagen -->
        <div class="lg:relative lg:max-w-none">
          <img src="assets/img/logo2.png" alt="Vista de la plataforma" class="w-[48rem] max-w-none rounded-xl shadow-xl shadow-indigo-300 ring-1 ring-gray-400/10 sm:w-[57rem] md:-ml-4 lg:-ml-0">
        </div>
      </div>
    </div>
  </section>


  <section id="como-funciona" class="py-16">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-4xl font-bold text-gray-900 mb-6">¿Como funciona?</h2>
      <p class="text-lg text-gray-600 mb-8">
      En tres simples pasos, nuestra plataforma optimiza todo el proceso de asignación de vehículos y distribución de pedidos.
      </p>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Paso 1 -->
        <div class="p-6 bg-white rounded-lg shadow">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">1. Ingreso de Pedidos</h3>
          <p class="text-gray-600">
            Ingresa los pedidos en el sistema, indicando las ubicaciones de entrega, prioridades y restricciones de tiempo.
          </p>
        </div>
        <!-- Paso 2 -->
        <div class="p-6 bg-white rounded-lg shadow">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">2. Asignación de Vehículos</h3>
          <p class="text-gray-600">
            Selecciona o deja que el sistema asigne más vehículos más adecuados para cada ruta.
          </p>
        </div>
        <!-- Paso 3 -->
        <div class="p-6 bg-white rounded-lg shadow">
          <h3 class="text-2xl font-semibold text-gray-800 mb-4">3. Optimización de Rutas</h3>
          <p class="text-gray-600">
            Con el sistema de asignación de vehículos, optimiza las rutas de entrega de tus pedidos.
          </p>
        </div>
      </div>
    </div>
  </section>

    <!-- CTA : Call To Action -->
  <section class="py-16 bg-white">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-4xl font-bold text-gray-900 mb-6">¿Aún no te has registrado?</h2>
        <p class="text-lg text-gray-600">Registrate ahora y descubre como nuestra plataforma puede mejorar tus operaciones logísticas.</p>
        <div class="flex justify-center space-x-6 py-5">
            <a href="login/r.php" class="px-6 py-3 border border-indigo-800 text-indigo-800 rounded-lg hover:bg-indigo-800 hover:text-white transition duration-300">
                Regístrate ahora
            </a>
        </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-800 py-6 text-center text-white">
    <p>&copy; 2024 OptimizaRutas. Todos los derechos reservados.</p>
  </footer>

</body>
</html>
