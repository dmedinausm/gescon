<h1>Página de Login</h1>
<p>Bienvenido, por favor inicia sesión.</p>
<!-- <a href="?page=main">Ir a Main</a> -->


<form action="?page=login" method="post"> <br>
  <input type="text" name="rut" placeholder="RUT" required><br>
  <input type="email" name="email" placeholder="Email" required><br>
  <input type="password" name="password" placeholder="Contraseña" required><br>
  <button type="submit">Ingresar</button>
</form>
<p>¿No tienes cuenta? <a href="?page=register">Regístrate aquí</a></p>

<?php

session_start();

$pdo = new PDO("mysql:host=localhost; dbname=gescon", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// obtener datos del formulario
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $rut = $_POST['rut'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    // buscar al usuario por email
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE RUT_usuario =  ?");
    $stmt->execute ([$rut]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        echo "Usuario no encontrado.";
        exit;
    }

    if($usuario && password_verify($password, $usuario['password_hash'])){
        //inicio de sesión correcto. 
        $_SESION['usuario'] = $usuario['RUT_usuario'];
        // $_SESSION['nombre'] = $usuario['nombre'];
        header("Location: ?page=main");
        exit;
    } 
    else {
        echo "Rut, email o contraseña incorrectos. ";
    }
}


// $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = : email")


?> 