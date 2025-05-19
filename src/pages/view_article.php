<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Artículos Subidos</title>
    <link rel="stylesheet" href="src/styles/article/view_article.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Tus artículos</h2>
        </div>

        <form method="get" action="">
            <input type="hidden" name="page" value="view_article">
            <input type="text" name="q" placeholder="Buscar..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button type="submit">Buscar</button>
        </form>

        <div class="nav-links">
            <a href="?page=post_article">Subir otro artículo</a>
            <a href="?page=main">Volver</a>
        </div>

        <?php
        session_start();
        try {
            $pdo = new PDO("mysql:host=localhost; dbname=gescon", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("
                SELECT a.ID_articulo, a.titulo, a.fecha_envio, a.resumen
                FROM articulo a
                JOIN articulo_autor aa ON a.ID_articulo = aa.ID_articulo
                WHERE aa.RUT_autor = ?
                ORDER BY a.fecha_envio DESC
            ");
            $stmt->execute([$_SESSION['usuario']]);
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($articles) === 0) {
                echo "<p class='no-articles'>No hay artículos registrados.</p>";
            } else {
                foreach ($articles as $article) {
                    echo "<div class='article'>";
                    echo "<h3>" . htmlspecialchars($article['titulo']) . "</h3>";
                    echo "<p><strong>Fecha de envío:</strong> " . htmlspecialchars($article['fecha_envio']) . "</p>";

                    // Tópicos
                    $stmt_topics = $pdo->prepare("
                        SELECT t.nombre_topico
                        FROM articulo_topico at
                        JOIN topico t ON at.ID_topico = t.ID_topico
                        WHERE at.ID_articulo = ?
                    ");
                    $stmt_topics->execute([$article['ID_articulo']]);
                    $topics = $stmt_topics->fetchAll(PDO::FETCH_COLUMN);
                    echo "<p><strong>Tópicos:</strong> " . htmlspecialchars(implode(", ", $topics)) . "</p>";

                    // Resumen
                    echo "<p><strong>Resumen:</strong> " . htmlspecialchars($article['resumen']) . "</p>";

                    // Autores
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
                        echo "<li>" . htmlspecialchars($author['nombre']) . " (" . htmlspecialchars($author['RUT_autor']) . ")$contactMark</li>";
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
                // Check if any reviewers are assigned to this article
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM articulo_revisor WHERE ID_articulo = ?");
                $stmt->execute([$article['ID_articulo']]);
                $hasReviewers = $stmt->fetchColumn() > 0;
            
                // Check if any reviews exist for this article
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM revision_articulo WHERE ID_articulo = ?");
                $stmt->execute([$article['ID_articulo']]);
                $reviewCount = $stmt->fetchColumn();
            
                // Allow editing only if either:
                // - No reviewers are assigned, or
                // - At least one review already exists
                if (!$hasReviewers && $reviewCount == 0) {
                    echo "<p><a href='?page=edit_article&id={$article['ID_articulo']}'>Editar o eliminar artículo</a></p>";
                }
            
                // Show link to reviews if any exist
                if ($reviewCount > 0) {
                    echo "<p><a href='?page=review_list&id={$article['ID_articulo']}'>Ver revisiones del artículo</a></p>";
                }
            }

            echo "</div>";
            
        }
    }

} catch (PDOException $e) {
    echo "<p>Error al obtener los artículos: " . $e->getMessage() . "</p>";
}
?>

</body>
</html>