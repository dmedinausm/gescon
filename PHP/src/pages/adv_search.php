

<!DOCTYPE html>
<html>
<head>
    <title>Artículos Subidos</title>
    <link rel="stylesheet" href="PHP/src/styles/article/adv_search.css">
</head>
<body>
<div class="container">
    <h1>Lista de Artículos Subidos</h1>
    <form method="get" action="">
        <input type="hidden" name="page" value="adv_search">
        
        <label for="search">Buscar por título:</label>
        <input type="text" name="search" placeholder="Título" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

        <label for="autor">Filtrar por autor:</label>
        <input type="text" name="autor" placeholder="Nombre del autor" value="<?= htmlspecialchars($_GET['autor'] ?? '') ?>">

        <label for="revisor">Filtrar por revisor:</label>
        <input type="text" name="revisor" placeholder="Nombre del revisor" value="<?= htmlspecialchars($_GET['revisor'] ?? '') ?>">

        <label>Filtrar por fecha de envío:</label>
        <input type="text" name="day" placeholder="Día" size="2" value="<?= htmlspecialchars($_GET['day'] ?? '') ?>">
        <input type="text" name="month" placeholder="Mes" size="2" value="<?= htmlspecialchars($_GET['month'] ?? '') ?>">
        <input type="text" name="year" placeholder="Año" size="4" value="<?= htmlspecialchars($_GET['year'] ?? '') ?>">

        <label>Filtrar por Tópico(s):</label>
        <div class="checkbox-group">
            <?php
            $pdo = new PDO("mysql:host=localhost; dbname=gescon", "root", "");
            $topicStmt = $pdo->query("SELECT ID_topico, nombre_topico FROM topico");
            $filterTopics = $topicStmt->fetchAll(PDO::FETCH_ASSOC);
            $selectedTopics = $_GET['topico'] ?? [];
            
            foreach ($filterTopics as $topic) {
                $checked = in_array($topic['ID_topico'], (array)$selectedTopics) ? 'checked' : '';
                echo "<label><input type='checkbox' name='topico[]' value='{$topic['ID_topico']}' $checked> {$topic['nombre_topico']}</label>";
            }
            ?>
        </div>

        <button type="submit">Buscar</button>
    </form>
    <?php
    session_start();
    try {   
        $pdo = new PDO("mysql:host=localhost; dbname=gescon", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // Get all articles
        $search = $_GET['search'] ?? '';
        $authorFilter = $_GET['autor'] ?? '';
        $revisorFilter = $_GET['revisor'] ?? '';
        $day = $_GET['day'] ?? '';
        $month = $_GET['month'] ?? '';
        $year = $_GET['year'] ?? '';
        $conditions = [];
        $params = [];
        
        // Search term
        if (!empty($_GET['search'])) {
            $conditions[] = "a.titulo LIKE :titulo";
            $params['titulo'] = '%' . $_GET['search'] . '%';
        }
        
        // Filter by author name
        if (!empty($_GET['autor'])) {
            $conditions[] = "u.nombre LIKE :autor";
            $params['autor'] = '%' . $_GET['autor'] . '%';
        }
    
        // Filter by reviewer name
        //if (!empty($_GET['revisor'])) {
            //$conditions[] = "u.nombre LIKE :revisor";
            //$params['revisor'] = '%' . $_GET['revisor'] . '%';
        //}    
    
        // Filter by date
        $dateParts = [];
        if (!empty($_GET['year']))  $dateParts[] = $_GET['year'];
        if (!empty($_GET['month'])) $dateParts[] = str_pad($_GET['month'], 2, '0', STR_PAD_LEFT);
        if (!empty($_GET['day']))   $dateParts[] = str_pad($_GET['day'], 2, '0', STR_PAD_LEFT);
        if (!empty($dateParts)) {
            $dateStr = implode('-', $dateParts);
            $conditions[] = "a.fecha_envio LIKE :fecha";
            $params['fecha'] = $dateStr . '%';
        }
          
        // Filter by selected topics (checkboxes)
        if (!empty($_GET['topico']) && is_array($_GET['topico'])) {
            $placeholders = [];
            foreach ($_GET['topico'] as $index => $topico_id) {
                $key = "topico_$index";
                $placeholders[] = ":$key";
                $params[$key] = $topico_id;
            }
            $conditions[] = "at.ID_topico IN (" . implode(",", $placeholders) . ")";
        }
        $where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";
        
        $sql = "
            SELECT DISTINCT a.ID_articulo, a.titulo, a.fecha_envio, a.resumen, t.nombre_topico
            FROM articulo a
            JOIN articulo_topico at ON a.ID_articulo = at.ID_articulo
            JOIN topico t ON at.ID_topico = t.ID_topico
            JOIN articulo_autor aa ON a.ID_articulo = aa.ID_articulo
            JOIN usuario u ON aa.RUT_autor = u.RUT_usuario
    
            $where
            ORDER BY a.fecha_envio DESC
        ";
        //JOIN articulo_revisor ar ON a.ID_articulo = aa.ID_articulo
        //JOIN usuario ur ON ar.RUT_revisor = u.RUT_usuario
        
        // pa testear
        //echo "<pre>$sql</pre>";
        //print_r($params);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($articles) === 0) {
            echo "<p>No hay artículos registrados.</p>";
        }  
        
        // else {
        //     foreach ($articles as $article) {
        //         echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>";
        //         echo "<h2>{$article['titulo']}</h2>";
        //         echo "<p><strong>Fecha de envío:</strong> {$article['fecha_envio']}</p>";
        //         // Get all topics for this article
        //         $stmt_topics = $pdo->prepare("
        //         SELECT t.nombre_topico
        //         FROM articulo_topico at
        //         JOIN topico t ON at.ID_topico = t.ID_topico
        //         WHERE at.ID_articulo = ?
        //         ");
        //         $stmt_topics->execute([$article['ID_articulo']]);
        //         $topics = $stmt_topics->fetchAll(PDO::FETCH_COLUMN);
    
        //         echo "<p><strong>Tópicos:</strong> " . implode(", ", $topics) . "</p>";
    
        //         echo "<p><strong>Resumen:</strong> {$article['resumen']}</p>";
    
        //         // Get authors
        //         $stmt2 = $pdo->prepare("
        //             SELECT aa.RUT_autor, u.nombre, aa.is_contact
        //             FROM articulo_autor aa
        //             JOIN usuario u ON aa.RUT_autor = u.RUT_usuario
        //             WHERE aa.ID_articulo = ?
        //         ");
        //         $stmt2->execute([$article['ID_articulo']]);
        //         $authors = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
        //         echo "<p><strong>Autores:</strong><ul>";
        //         foreach ($authors as $author) {
        //             $contactMark = $author['is_contact'] ? " (autor de contacto)" : "";
        //             echo "<li>{$author['nombre']} ({$author['RUT_autor']}){$contactMark}</li>";
        //         }
        //         echo "</ul></p>";
    
        //         $isAuthor = false;
        //         foreach ($authors as $author) {
        //             if (isset($_SESSION['usuario']) && $_SESSION['usuario'] === $author['RUT_autor']) {
        //                 $isAuthor = true;
        //                 break;
        //             }
        //         }
        //         if ($isAuthor) {
        //             echo "<p><a href='?page=edit_article&id={$article['ID_articulo']}'>Editar o eliminar artículo</a></p>";
        //         }
                
    
        //         echo "</div>";
                
        //     }
        // }
    
    } catch (PDOException $e) {
        echo "<p>Error al obtener los artículos: " . $e->getMessage() . "</p>";
    }
    ?>
<?php
if (count($articles) === 0) {
    echo "<p>No hay artículos registrados.</p>";
} else {
    echo '<div class="grid">';
    foreach ($articles as $article) {
        // Obtener tópicos
        $stmt_topics = $pdo->prepare("SELECT t.nombre_topico FROM articulo_topico at JOIN topico t ON at.ID_topico = t.ID_topico WHERE at.ID_articulo = ?");
        $stmt_topics->execute([$article['ID_articulo']]);
        $topics = $stmt_topics->fetchAll(PDO::FETCH_COLUMN);
        
        // Obtener autores
        $stmt2 = $pdo->prepare("SELECT aa.RUT_autor, u.nombre, aa.is_contact FROM articulo_autor aa JOIN usuario u ON aa.RUT_autor = u.RUT_usuario WHERE aa.ID_articulo = ?");
        $stmt2->execute([$article['ID_articulo']]);
        $authors = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        $isAuthor = false;
        foreach ($authors as $author) {
            if (isset($_SESSION['usuario']) && $_SESSION['usuario'] === $author['RUT_autor']) {
                $isAuthor = true;
                break;
            }
        }

        echo "<div class='card'>";
        echo "<h2>{$article['titulo']}</h2>";
        echo "<p class='date'>📅 {$article['fecha_envio']}</p>";
        echo "<p class='summary'>" . nl2br(htmlspecialchars($article['resumen'])) . "</p>";

        echo "<div class='topics'>";
        foreach ($topics as $topico) {
            echo "<span class='topic-tag'>$topico</span>";
        }
        echo "</div>";

        echo "<p><strong>Autores:</strong></p><ul>";
        foreach ($authors as $author) {
            $contactMark = $author['is_contact'] ? " (autor de contacto)" : "";
            echo "<li>{$author['nombre']} ({$author['RUT_autor']})$contactMark</li>";
        }
        echo "</ul>";

        if ($isAuthor) {
            echo "<a class='edit-link' href='?page=edit_article&id={$article['ID_articulo']}'>✏️ Editar o eliminar</a>";
        }

        echo "</div>";
    }
    echo '</div>';
}
?>


    <a href="?page=main" class="back-link">← Volver al menú</a>

    <!-- Aquí se imprimen los resultados de búsqueda -->
    <!-- Mantén tu lógica PHP aquí para mostrar los artículos -->
</div>
</body>
</html>
