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

// Check if current user is an author of the article
$stmt = $pdo->prepare("
    SELECT 1 FROM articulo_autor
    WHERE ID_articulo = ? AND RUT_autor = ?
");
$stmt->execute([$article_id, $currentUser]);

if ($stmt->rowCount() === 0) {
    die("Acceso denegado. Solo los autores del artículo pueden ver esta página.");
}

// Fetch article info
$stmt = $pdo->prepare("SELECT titulo FROM articulo WHERE ID_articulo = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if (!$article) {
    die("Artículo no encontrado.");
}

echo "<h1>Reseñas del artículo: " . htmlspecialchars($article['titulo']) . "</h1>";

// Fetch reviews linked to this article
$stmt = $pdo->prepare("
    SELECT e.ID_revision, e.nombre_revision
    FROM evaluacion e
    JOIN revision_articulo ra ON e.ID_revision = ra.ID_revision
    WHERE ra.ID_articulo = ?
");
$stmt->execute([$article_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$reviews) {
    echo "<p>No hay reseñas registradas para este artículo aún.</p>";
} else {
    echo "<ul>";
    foreach ($reviews as $review) {
        $name = htmlspecialchars($review['nombre_revision']);
        $link = "?page=view_review&id={$article_id}";
        echo "<li>$name - <a href='$link'>Ver detalle</a></li>";
    }
    echo "</ul>";
}

echo "<p><a href='?page=view_article'>Volver a la lista de artículos</a></p>";
?>