<!-- 
// session_start();

//si el usuario inición sesion?
// if(!isset($_SESSION['usuario'])) {
//     header("Location: login.php");
//     exit;
// }

// $nombre = $_SESSION['nombre']; -->

<h2> Bienvenido, 
    
    <!DOCTYPE html>
    <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Panel de Usuario</title>
        </head>
        <body>
            <h1>Página Principal</h1>
            <!-- <h2>Bienvenido, <?php echo htmlspecialchars($nombre); ?> 👋</h2> -->
            <p>Has iniciado sesión correctamente.</p>
            <a href="?page=login">Ir al login</a>
        </body>
</html>
