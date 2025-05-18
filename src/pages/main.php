<?php 
session_start();

if(!isset($_SESSION['usuario'])) {
    header("Location: ?page=login");
    // header("Location: ?page=post_article");
    // header("Location: ?page=view_article");
    exit();
}
include 'lib.php'

?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>GESCON - Panel Principal</title>
        <link rel="stylesheet" href="src\styles\style.css">
    </head>
    <body>
        <header>
            <h1>GESCON</h1>
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?> 👋</h2>
            <p>Tu rol es:  <?php echo $tipo_usuario ?>. </p>
        </header>

        
        
        <?php if($_SESSION['tipo_usuario'] === 'R'): ?>
            <p style="color:darkgreen;"> "Eres nuevo revisor!, ingresa a tu perfil para agregar tu especialidad."</p>
        <?php endif ?>
        
        <nav>

            <ul>
                <li><a href="?page=perfil">Perfil</a></li>
                <li><a href="?page=view_article">Ir a articulos</a></li>
                <li><a href="?page=adv_search">Ir a advsearch</a></li>
                    
                <?php if ($_SESSION['tipo_usuario'] === 'J'): ?>
                    <li><a href="?page=gestion_revisores">Comité de Revisores</a></li>
                    <li><a href="?page=asignar_artic_rev">Asignar Articulos</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <footer>
            <a href="?page=login">Cerrar sesión</a>
        </footer>
    </body>
</html>
