<!DOCTYPE html>
<html>
<head>
  <title>Registro</title>
</head>
<body>
<h1>Página de Registro</h1>
<p>Bienvenido, por favor registra tu usuario.</p>


<form action="?page=register" method="post"><br>
  
  <input type="text" name="rut" placeholder="RUT" required><br>
  <input type="text" name="name" placeholder="Nombre" required><br>
  <input type="email" name="email" placeholder="Email" required><br>
  <input type="password" name="password" placeholder="Contraseña" required><br>
  <button type="submit">Ingresar</button>
</form><br>

<p>¿Ya tienes cuenta? <a href="?page=login">Inicia sesión aquí</a></p>
</body>
</html>

<?php

require_once 'config\db.php';

// $pdo = new PDO("mysql:host=localhost; dbname=gescon", "root", "");
// $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ATTR_ERRMODE es una constante que representa la opción de manejo de errores
// ERRMODE_EXCEPTION si algo sale mal, lanza un exception. 

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $rut = trim($_POST['rut']);
    $nombre = trim($_POST['name']);
    $email = trim($_POST['email']);
    $user_type = 'A';
    $password = $_POST['password'];
    
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "Usuario registrado correctamente.";
    try {

        $stmt = $pdo->prepare("INSERT INTO usuario (RUT_usuario, nombre, email, tipo_usuario, password_hash) VALUES (?,?,?,?,?)");
        $stmt->execute([$rut,$nombre,$email,$user_type,$password_hash]);
        
        echo "Usuario registrado correctamente.";
    } catch (PDOExeption $e) {
        if($e->getCode() === '23000') {
            echo "El RUT o correo ya están registrados";
        } else {
            echo "Error al registrar: ". $e->getMessage();
        }
    }
}
?>
