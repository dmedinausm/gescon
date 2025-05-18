<?php 
session_start();

if(!isset($_SESSION['usuario'])) {
    header("Location: ?page=login");
    header("Location: ?page=post_article");
    header("Location: ?page=view_article");
    exit();
}
include 'lib.php'




?>



<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>MAIN</title>
    </head>
    <body>
        <h1>GESCON</h1>
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?> 👋</h2>
        <p>Tu rol es:  <?php echo $tipo_usuario ?>. </p>
        <p>Has iniciado sesión correctamente.</p>
        <a href="?page=login">Ir al login</a>
        <a href="?page=view_article">Ir a articulos</a>
        <a href="?page=adv_search">Ir a advsearch</a>
    
                
        <ul>
        <li><a href="perfil.php">Perfil</a></li>

        <?php if ($_SESSION['tipo_usuario'] === 'J'): ?>
        <li><a href="?page=gestion_revisores">Comité de Revisores</a></li>
        <li><a href="?page=asignar_articulos">Asignar Articulos</a></li>

        <?php endif; ?>
        </ul>
            
    </body>
</html>
