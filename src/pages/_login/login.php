
<?php

require_once 'config\db.php';
session_start();

// $pdo = new PDO("mysql:host=localhost; dbname=gescon", "root", "");
// $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
        $_SESSION['usuario'] = $usuario['RUT_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];
        header("Location: ?page=main");
        exit;
    } 
    else {
        echo "Rut, email o contraseña incorrectos. ";
    }
}

?> 

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página de Login</title>
    <link rel="stylesheet" href="src/styles/style.css">
    <link rel="stylesheet" href="src/styles/login/login.css">
</head>
<body>

    <div class="form-container">
        <h1>Página de Login</h1>
        <p>Bienvenido, por favor inicia sesión.</p>

        <form action="?page=login" method="post">
            <input type="text" name="rut" placeholder="RUT" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Ingresar</button>
        </form>

        <p class="link-text">¿No tienes cuenta? <a href="?page=register">Regístrate aquí</a></p>
    </div>

</body>
</html>

