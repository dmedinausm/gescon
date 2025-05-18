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
        <title>GESCON - Panel Principal</title>
        <link rel="stylesheet" href="src\styles\style.css">
    </head>
    <body>
        <h1>GESCON</h1>
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?> 👋</h2>
        <p>Tu rol es:  <?php echo $tipo_usuario ?>. </p>
        <p>Has iniciado sesión correctamente.</p>
        <a href="?page=login">Ir al login</a>
        <a href="?page=view_article">Ir a articulos</a>
        <a href="?page=adv_search">Ir a advsearch</a>
    
                
        <?php if($_SESSION['tipo_usuario'] === 'R') 
            echo "Eres nuevo revisor!, ingresa a tu perfil para agregar tu especialidad."
        ?>
        <?php if ($_SESSION['tipo_usuario'] === 'R'): ?>
        <a href="?page=review_article">Ir a la pega</a>
        <?php endif; ?>
        <ul>
        <li><a href="?page=perfil">Perfil</a></li>
        

        <?php if ($_SESSION['tipo_usuario'] === 'J'): ?>
        <li><a href="?page=gestion_revisores">Comité de Revisores</a></li>
        <li><a href="?page=asignar_articulos">Asignar Articulos</a></li>
        </body>
        </html>
        <?php endif; ?>

        <h1>Lista de Artículos Subidos</h1>
        <form method="get" action="">
            <input type="hidden" name="page" value="main">
            <input type="text" name="q" placeholder="Buscar..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button type="submit">Buscar</button>
        </form>
        <?php
        try {   
            $pdo = new PDO("mysql:host=localhost; dbname=gescon", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Session RUT: " . $_SESSION['usuario'];

            // Get all articles
            $search = $_GET['q'] ?? '';
            $params = [];
            
            $sql = "
                SELECT DISTINCT a.ID_articulo, a.titulo, a.fecha_envio, a.resumen, t.nombre_topico
                FROM articulo a
                JOIN articulo_topico at ON a.ID_articulo = at.ID_articulo
                JOIN topico t ON at.ID_topico = t.ID_topico
                LEFT JOIN articulo_autor aa ON a.ID_articulo = aa.ID_articulo
                LEFT JOIN usuario u ON aa.RUT_autor = u.RUT_usuario
            ";
        
            if (!empty($search)) {
                $sql .= " WHERE 
                    a.titulo LIKE :q OR 
                    a.resumen LIKE :q OR 
                    t.nombre_topico LIKE :q OR 
                    u.nombre LIKE :q
                ";
                $params['q'] = '%' . $search . '%';
            }
        
            $sql .= " ORDER BY a.fecha_envio DESC";
        
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
               
            
            
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($articles) === 0) {
                echo "<p>No hay artículos registrados.</p>";
            }  
        
            else {
                foreach ($articles as $article) {
                    echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>";
                    echo "<h2>{$article['titulo']}</h2>";
                    echo "<p><strong>Fecha de envío:</strong> {$article['fecha_envio']}</p>";
                    // Get all topics for this article
                    $stmt_topics = $pdo->prepare("
                    SELECT t.nombre_topico
                    FROM articulo_topico at
                    JOIN topico t ON at.ID_topico = t.ID_topico
                    WHERE at.ID_articulo = ?
                    ");
                    $stmt_topics->execute([$article['ID_articulo']]);
                    $topics = $stmt_topics->fetchAll(PDO::FETCH_COLUMN);

                    echo "<p><strong>Tópicos:</strong> " . implode(", ", $topics) . "</p>";

                    echo "<p><strong>Resumen:</strong> {$article['resumen']}</p>";

                    // Get authors
                    $stmt2 = $pdo->prepare("
                        SELECT aa.RUT_autor, u.nombre, aa.is_contact
                        FROM articulo_autor aa
                        JOIN usuario u ON aa.RUT_autor = u.RUT_usuario
                        WHERE aa.ID_articulo = ?
                    ");
                    $stmt2->execute([$article['ID_articulo']]);
                    $authors = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                    echo "<p><strong>Autores:</strong><ul>";
                    foreach ($authors as $author) {
                        $contactMark = $author['is_contact'] ? " (autor de contacto)" : "";
                        echo "<li>{$author['nombre']} ({$author['RUT_autor']}){$contactMark}</li>";
                    }
                    echo "</ul></p>";
                    

                    echo "</div>";
                    
                }
            }

        } catch (PDOException $e) {
            echo "<p>Error al obtener los artículos: " . $e->getMessage() . "</p>";
        }
        ?>
        </ul>
    <footer>
        <a href="?page=login">Cerrar sesión</a>
    </footer>        
    </body>
</html>
