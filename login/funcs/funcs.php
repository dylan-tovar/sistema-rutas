<?php


	function isNull($nombre, $user, $pass, $pass_con, $email){
		if(strlen(trim($nombre)) < 1 || strlen(trim($user)) < 1 || strlen(trim($pass)) < 1 || strlen(trim($pass_con)) < 1 || strlen(trim($email)) < 1)
		{
			return true;
			} else {
			return false;
		}		
	}
	
	function isEmail($email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)){
			return true;
			} else {
			return false;
		}
	}
	
	function validaPassword($var1, $var2)
	{
		if (strcmp($var1, $var2) !== 0){
			return false;
			} else {
			return true;
		}
	}
	
	function usuarioExiste($usuario)
	{
		global $mysqli;
		
		$stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE usuario = ? LIMIT 1");
		$stmt->bind_param("s", $usuario);
		$stmt->execute();
		$stmt->store_result();
		$num = $stmt->num_rows;
		$stmt->close();
		
		if ($num > 0){
			return true;
			} else {
			return false;
		}
	}
	
	function emailExiste($email)
	{
		global $mysqli;
		
		$stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE correo = ? LIMIT 1");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->store_result();
		$num = $stmt->num_rows;
		$stmt->close();
		
		if ($num > 0){
			return true;
			} else {
			return false;	
		}
	}
	
	function generateToken()
	{
		$gen = md5(uniqid(mt_rand(), false));	
		return $gen;
	}
	
	function hashPassword($password) 
	{
		$hash = password_hash($password, PASSWORD_DEFAULT);
		return $hash;
	}
	
	function resultBlock($errors){
		if(count($errors) > 0)
		{
			echo "<div id='error' class='alert alert-danger' role='alert'>
			<a href='#' onclick=\"showHide('error');\">[X]</a>
			<ul>";
			foreach($errors as $error)
			{
				echo "<li>".$error."</li>";
			}
			echo "</ul>";
			echo "</div>";
		}
	}
	
	function registraUsuario($usuario, $pass_hash, $nombre, $email, $id_tipo) {
        global $mysqli;
    
        // Asegúrate de que la consulta tenga exactamente los mismos valores que columnas
        $stmt = $mysqli->prepare("INSERT INTO usuarios (usuario, password, nombre, correo, last_session, id_tipo) VALUES (?, ?, ?, ?, NOW(), ?)");
        
        // Este es el formato correcto, 5 variables: usuario, password, nombre, correo, id_tipo
        $stmt->bind_param('ssssi', $usuario, $pass_hash, $nombre, $email, $id_tipo);
    
        if ($stmt->execute()) {
            return $mysqli->insert_id; // Devuelve el ID del nuevo registro
        } else {
            return 0; // Retorna 0 en caso de error
        }
    }
	
	function isNullLogin($usuario, $password){
		if(strlen(trim($usuario)) < 1 || strlen(trim($password)) < 1)
		{
			return true;
		}
		else
		{
			return false;
		}		
	}
	
	function login($usuario, $password)
	{
		global $mysqli;
		
		$stmt = $mysqli->prepare("SELECT id, id_tipo, password FROM usuarios WHERE usuario = ? || correo = ? LIMIT 1");
		$stmt->bind_param("ss", $usuario, $usuario);
		$stmt->execute();
		$stmt->store_result();
		$rows = $stmt->num_rows;
		
		if($rows > 0) {
				
			$stmt->bind_result($id, $id_tipo, $passwd);
			$stmt->fetch();
			
			$validaPassw = password_verify($password, $passwd);
			
			if($validaPassw){
				
				lastSession($id);
				$_SESSION['id_usuario'] = $id;
				$_SESSION['tipo_usuario'] = $id_tipo;
				
				// header("location: ../dashboard/index.php");

				switch($id_tipo) {
					case 1:
						header("location: ../dashboard/admin/");
						break;
					case 2:
						header("location: ../dashboard/");
						break;
					case 3:
						header("location: ../dashboard/dealer/");
						break;
				}
			} else {
				
				$errors = "La contrase&ntilde;a es incorrecta";
			}
		
	    } else {
		 	$errors = "El nombre de usuario o correo electr&oacute;nico no existe";
	    }
		return $errors;
	}
	
	function lastSession($id)
	{
		global $mysqli;
		
		$stmt = $mysqli->prepare("UPDATE usuarios SET last_session=NOW() /*, token_password='', password_request=0*/ WHERE id = ?");
		$stmt->bind_param('s', $id);
		$stmt->execute();
		$stmt->close();
	}