<?php

  require '../db/conexion.php';
  require 'funcs/funcs.php';

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $errors = array();
    $nombre = $usuario = $email = '';

    if (!empty($_POST)) {
        $nombre = $mysqli->real_escape_string($_POST['nombre']);
        $usuario = $mysqli->real_escape_string($_POST['usuario']);
        $password = $mysqli->real_escape_string($_POST['password']);
        $con_password = $mysqli->real_escape_string($_POST['con_password']);
        $email = $mysqli->real_escape_string($_POST['email']);
        
        $activo = 0;
        $tipo_usuario = 2;

        if (isNull($nombre, $usuario, $password, $con_password, $email)) {
            $errors[] = "Debe llenar todos los campos.";
        }

        if (!isEmail($email)) {
            $errors[] = "Dirección de correo electrónico no válida.";
        }

        if (!validaPassword($password, $con_password)) {
            $errors[] = "Las contraseñas no coinciden.";
        }

        if (usuarioExiste($usuario)) {
            $errors[] = "El nombre de usuario $usuario ya existe.";
        }

        if (emailExiste($email)) {
            $errors[] = "El correo electrónico $email ya existe.";
        }

        if (count($errors) == 0) {
          $pass_hash = hashPassword($password);
          $registro = registraUsuario($usuario, $pass_hash, $nombre, $email, $tipo_usuario);
      
          if ($registro > 0) {
            header("Location: r_success.php");
            exit;
          } else {
              $errors[] = "Error al registrar el usuario.";
          }
      }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<section class="bg-gray-50 min-h-screen flex items-center justify-center">
  <div class="bg-gray-100 flex rounded-2xl shadow-2xl max-w-4xl p-5 items-center">
    <div class="md:w-1/2 px-10 md:px-18 py-10">
      <h2 class="font-bold text-2xl text-[#001B4D]">Registrate</h2>
      <p class="text-xs mt-3 text-[#001B4D]">👋 Hola, te damos la bienvenida</p>

      <!-- Form -->
      <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="flex flex-col gap-4">
        <input class="p-2 mt-8 rounded-xl border focus:ring-2 focus:ring-indigo-600 hover:border-indigo-600" type="text" name="nombre" placeholder="Nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
        
        <input class="p-2 rounded-xl border focus:ring-2 focus:ring-indigo-600 hover:border-indigo-600" type="text" name="usuario" placeholder="Usuario" value="<?php echo htmlspecialchars($usuario); ?>" required>
        
        <input class="p-2 rounded-xl border focus:ring-2 focus:ring-indigo-600 hover:border-indigo-600" type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
        
        <div class="relative">
          <input class="p-2 rounded-xl border w-full focus:ring-2 focus:ring-indigo-600 hover:border-indigo-600" type="password" name="password" placeholder="Contraseña" required>
        </div>
        
        <div class="relative">
          <input class="p-2 rounded-xl border w-full focus:ring-2 focus:ring-indigo-600 hover:border-indigo-600" type="password" name="con_password" placeholder="Confirmar contraseña" required>
        </div>
        
        <button class="bg-[#001B4D] rounded-xl text-white py-2 hover:scale-105 duration-300 focus:ring-4 focus:ring-indigo-600" type="submit">Register</button>
      </form>
      
      <?php if (!empty($errors)): ?>
        <div class="mt-2 text-xs bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded relative">
           <?php echo resultBlock($errors); ?>
        </div>
      <?php endif; ?>

      <div class="mt-16 text-xs border-b border-[#001B4D] py-4 text-[#001B4D]">
        <a href="#"></a>
      </div>

      <div class="mt-3 text-xs flex justify-between items-center text-[#001B4D]">
        <p>Tienes una cuenta?</p>
        <a href="../login/">
          <button class="py-2 px-5 bg-white border rounded-xl hover:scale-110 duration-300 hover:border-indigo-600 focus:ring-2 focus:ring-indigo-600">Inicia Sesión</button>
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