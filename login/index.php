<?php

	session_start();
	require '../db/conexion.php';
	require 'funcs/funcs.php';

	$errors = array();

	if(!empty($_POST)) {
		$usuario = $mysqli->real_escape_string($_POST['usuario']); 
		$password = $mysqli->real_escape_string($_POST['password']);

		if(isNullLogin($usuario, $password)) {
			$errors[] = "Debe llenar todos los campos.";
		}

		$errors[] = login($usuario, $password);
	}
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<section class="bg-gray-50 min-h-screen flex items-center justify-center">
  <div class="bg-gray-100 flex rounded-2xl shadow-2xl max-w-4xl p-5 items-center">
    <!-- Form -->
    <div class="md:w-1/2 px-10 md:px-18 py-10">
      <h2 class="font-bold text-2xl text-[#001B4D]">Iniciar Sesión</h2>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="flex flex-col gap-4">
        <input class="p-2 mt-8 rounded-xl border focus:ring-2 focus:ring-indigo-600 hover:border-indigo-600" type="text" name="usuario" placeholder="Usuario o Email" required>
        
        <div class="relative">
          <input class="p-2 rounded-xl border w-full focus:ring-2 focus:ring-indigo-600 hover:border-indigo-600" type="password" name="password" placeholder="Password" required>
        </div>
        
        <button class="bg-[#001B4D] rounded-xl text-white py-2 hover:scale-105 duration-300 focus:ring-4 focus:ring-indigo-600" type="submit">Login</button>
      </form>

      <?php if (!empty($errors)): ?>
        <div class="mt-2 text-xs bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded relative">
           <?php echo resultBlock($errors); ?>
        </div>
      <?php endif; ?>

      <div class="mt-16 text-xs border-b border-[#001B4D] py-4 text-[#001B4D]">
        <!-- <a href="#">Forgot your password?</a> -->
      </div>

      <div class="mt-3 text-xs flex justify-between items-center text-[#001B4D]">
        <p>No tienes una cuenta?</p>
        <a href="r.php">
          <button class="py-2 px-5 bg-white border rounded-xl hover:scale-110 duration-300 hover:border-indigo-600 focus:ring-2 focus:ring-indigo-600">Regístrate</button>
        </a>
      </div>
    </div>

    <!-- Imagen -->
    <div class="md:block hidden w-1/2">
      <img class="rounded-2xl" src="https://plus.unsplash.com/premium_photo-1681079527198-e69ea68a2d3f?fm=jpg&q=60&w=3000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8aW50ZXJzZWNjaW9uZXN8ZW58MHx8MHx8fDA%3D">
    </div>
  </div>
</section>
</body>
</html>