<!DOCTYPE html>
<html>
<head>
  <title>Registro</title>
</head>
<body>
<h1>Página de Registro</h1>
<p>Bienvenido, por favor registra tu usuario.</p>

<!-- <a href="main">Ir a Main</a> -->


<form action="register" method="post"><br>
  
  <input type="text" name="rut" placeholder="RUT" required><br>
  <input type="text" name="name" placeholder="Nombre" required><br>
  <input type="email" name="email" placeholder="Email" required><br>
  <input type="password" name="password" placeholder="Contraseña" required><br>
  <!-- <input type="select" name="user_type" option="1" required> -->
  <select name="tipo_usuario" required>
    <option value="A" selected>Autor</option> 
    <option value="R">Revisor</option>
    </select>
  <button type="submit">Ingresar</button>
</form><br>

<p>¿Ya tienes cuenta? <a href="login">Inicia sesión aquí</a></p>
</body>
</html>

<?php

$pdo = new PDO("mysql:host=localhost; dbname=gescon", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ATTR_ERRMODE es una constante que representa la opción de manejo de errores
// ERRMODE_EXCEPTION si algo sale mal, lanza un exception. 
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $rut = $_POST['rut'];
    $nombre = $_POST['name'];
    $email = $_POST['email'];
    $user_type = $_POST['tipo_usuario'];
    $password = $_POST['password'];
    
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO usuario (RUT_usuario, nombre, email, tipo_usuario, password_hash) VALUES (?,?,?,?,?)");
    $stmt->execute([$rut,$nombre,$email,$user_type,$password_hash]);
    
    echo "Usuario registrado correctamente.";
}
?>


<!-- if ($tipo === "autor") {
        $stmt = $pdo->prepare("INSERT INTO autor (RUT_autor) VALUES (?)");
        $stmt->execute([$rut]);
    } elseif ($tipo === "revisor") {
        $stmt = $pdo->prepare("INSERT INTO revisor (RUT_revisor) VALUES (?)");
        $stmt->execute([$rut]);
    } -->