<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=gescon", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if user is logged in
if (!isset($_SESSION['usuario'])) {
    die("Acceso denegado. Debes iniciar sesión.");
}

$currentUser = $_SESSION['usuario'];

// Validate article ID
if (!isset($_GET['id'])) {
    die("ID de artículo no proporcionado.");
}
$article_id = $_GET['id'];

// Check if the user is an author or a reviewer of the article
$stmt = $pdo->prepare("
    SELECT 1 FROM articulo_autor WHERE ID_articulo = ? AND RUT_autor = ?
    UNION
    SELECT 1 FROM articulo_revisor WHERE ID_articulo = ? AND RUT_revisor = ?
");
$stmt->execute([$article_id, $currentUser, $article_id, $currentUser]);

if ($stmt->rowCount() === 0) {
    die("Acceso denegado. No estás autorizado para ver estas reseñas.");
}

// Fetch article title
$stmt = $pdo->prepare("SELECT titulo FROM articulo WHERE ID_articulo = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if (!$article) {
    die("Artículo no encontrado.");
}

echo "<h1>Reseñas para el artículo: " . htmlspecialchars($article['titulo']) . "</h1>";

// Fetch evaluations
$stmt = $pdo->prepare("
    SELECT e.*
    FROM evaluacion e
    JOIN revision_articulo ra ON e.ID_revision = ra.ID_revision
    WHERE ra.ID_articulo = ?
");
$stmt->execute([$article_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$reviews) {
    echo "<p>No hay reseñas para este artículo.</p>";
} else {
    foreach ($reviews as $review) {
        echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:15px;'>";
        echo "<h3>" . htmlspecialchars($review['nombre_revision']) . "</h3>";
        echo "<p><strong>Calidad técnica:</strong> " . $review['calidad_tecnica'] . "/5</p>";
        echo "<p><strong>Originalidad:</strong> " . $review['originalidad'] . "/5</p>";
        echo "<p><strong>Valoración global:</strong> " . $review['valoracionGlobal'] . "/5</p>";
        echo "<p><strong>Argumentos:</strong> " . nl2br(htmlspecialchars($review['argumentos'])) . "</p>";
        if (!empty($review['comentario'])) {
            echo "<p><strong>Comentario:</strong> " . nl2br(htmlspecialchars($review['comentario'])) . "</p>";
        }
        echo "</div>";
    }
}

    echo "<p><a href='?page=review_list&id={$article_id}'>Volver a la lista de evaluaciones</a></p>";



?>