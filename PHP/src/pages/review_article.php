<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Artículos a Revisar</title>
    <link rel="stylesheet" href="PHP/src/styles/article/review_article.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Artículos a Revisar</h2>
            <a class="back-button" href="?page=main">Volver</a>
        </div>

        <form method="get" action="">
            <input type="hidden" name="page" value="review_article">
            <label for="busqueda">Buscar artículos</label>
            <input type="text" id="busqueda" name="q" placeholder="Buscar..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <input type="submit" value="Buscar">
        </form>
          <?php if (empty($articulos)) : ?>
            <div class="message error">No hay artículos para revisar.</div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
session_start();
try {   
    $pdo = new PDO("mysql:host=localhost; dbname=gescon", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // Get all articles
    if ($_SESSION['tipo_usuario'] != 'R') {
        die("Acceso denegado. Solo los revisores pueden editar este artículo.");
    }
    $stmt = $pdo->prepare("
        SELECT a.ID_articulo, a.titulo, a.fecha_envio, a.resumen, t.nombre_topico
        FROM articulo a
        JOIN articulo_topico at ON a.ID_articulo = at.ID_articulo
        JOIN topico t ON at.ID_topico = t.ID_topico
        JOIN articulo_autor aa ON a.ID_articulo = aa.ID_articulo
        JOIN articulo_revisor ar ON a.ID_articulo = ar.ID_articulo
        WHERE ar.RUT_revisor = ?
        ORDER BY a.fecha_envio DESC
    ");
    $stmt->execute([$_SESSION['usuario']]);
 
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($articles) === 0) {
        // echo "<p>No hay artículos para revisar.</p>";
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

            // Check if a review already exists for this article by this reviewer
            $stmtReviewCheck = $pdo->prepare("
                SELECT ra.ID_revision
                FROM revision_articulo ra
                JOIN evaluacion e ON ra.ID_revision = e.ID_revision
                JOIN articulo_revisor ar ON ra.ID_articulo = ar.ID_articulo
                WHERE ra.ID_articulo = ? AND ar.RUT_revisor = ?
                LIMIT 1
            ");
            $stmtReviewCheck->execute([$article['ID_articulo'], $_SESSION['usuario']]);
            $review = $stmtReviewCheck->fetch(PDO::FETCH_ASSOC);

            if ($review) {
                echo "<p><a href='?page=view_review_r&id={$review['ID_revision']}&articulo={$article['ID_articulo']}'>Ver evaluación enviada</a></p>";
            } else {
                echo "<p><a href='?page=add_review&id={$article['ID_articulo']}'>A revisar</a></p>";
            }
            echo "</div>";
            
        }
    }

} catch (PDOException $e) {
    echo "<p>Error al obtener los artículos: " . $e->getMessage() . "</p>";
}
?>

