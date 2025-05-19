<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="PHP/src/styles/style.css"> <!-- Ajusta la ruta si es necesario -->
    <link rel="stylesheet" href="PHP/src/styles/login/register.css"> <!-- Ajusta la ruta si es necesario -->

</head>
<body>

    <div class="form-container">
        <h1>Página de Registro</h1>
        <p>Bienvenido, por favor registra tu usuario.</p>

        <form action="?page=register" method="post">
            <input type="text" name="rut" placeholder="RUT" required>
            <input type="text" name="name" placeholder="Nombre" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Registrarse</button>
        </form>

        <p class="link-text">¿Ya tienes cuenta? <a href="?page=login">Inicia sesión aquí</a></p>
    </div>

</body>
</html>

<?php

require_once 'config\db.php';

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
