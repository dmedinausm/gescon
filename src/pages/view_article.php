<!DOCTYPE html>
<html>
<head>
    <title>Artículos Subidos</title>
</head>
<body>
<h1>Lista de Artículos Subidos</h1>
<form method="get" action="">
    <input type="hidden" name="page" value="view_article">
    <label>
        <input type="checkbox" name="solo_mios" value="1" <?php if (isset($_GET['solo_mios'])) echo "checked"; ?>>
        Mostrar solo mis artículos
    </label>
    <button type="submit">Filtrar</button>
</form>

<?php
session_start();
try {   
    $pdo = new PDO("mysql:host=localhost; dbname=gescon", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Session RUT: " . $_SESSION['usuario'];

    // Get all articles
    $solo_mios = isset($_GET['solo_mios']) && isset($_SESSION['usuario']);  
    if ($solo_mios) {
        $stmt = $pdo->prepare("
            SELECT a.ID_articulo, a.titulo, a.fecha_envio, a.resumen, t.nombre_topico
            FROM articulo a
            JOIN articulo_topico at ON a.ID_articulo = at.ID_articulo
            JOIN topico t ON at.ID_topico = t.ID_topico
            JOIN articulo_autor aa ON a.ID_articulo = aa.ID_articulo
            WHERE aa.RUT_autor = ?
            ORDER BY a.fecha_envio DESC
        ");
        $stmt->execute([$_SESSION['usuario']]);
    } 
    else {
        $stmt = $pdo->query("
            SELECT a.ID_articulo, a.titulo, a.fecha_envio, a.resumen, t.nombre_topico
            FROM articulo a
            JOIN articulo_topico at ON a.ID_articulo = at.ID_articulo
            JOIN topico t ON at.ID_topico = t.ID_topico
            ORDER BY a.fecha_envio DESC
        ");
    }    
    
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($articles) === 0) {
        echo "<p>No hay artículos registrados.</p>";
    }  
  
    else {
        foreach ($articles as $article) {
            echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>";
            echo "<h2>{$article['titulo']}</h2>";
            echo "<p><strong>Fecha de envío:</strong> {$article['fecha_envio']}</p>";
            echo "<p><strong>Tópico:</strong> {$article['nombre_topico']}</p>";
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

            $isAuthor = false;
            foreach ($authors as $author) {
                if (isset($_SESSION['usuario']) && $_SESSION['usuario'] === $author['RUT_autor']) {
                    $isAuthor = true;
                    break;
                }
            }
            if ($isAuthor) {
                echo "<p><a href='?page=edit_article&id={$article['ID_articulo']}'>Editar o eliminar artículo</a></p>";
            }
            

            echo "</div>";
            
        }
    }

} catch (PDOException $e) {
    echo "<p>Error al obtener los artículos: " . $e->getMessage() . "</p>";
}
?>

<p><a href="?page=post_article">Subir otro artículo</a></p>

</body>
</html>