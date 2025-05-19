<?php
session_start();

// DB connection
$pdo = new PDO("mysql:host=localhost;dbname=gescon", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check session
if (!isset($_SESSION['usuario'])) {
    die("Acceso denegado. Debes iniciar sesión.");
}

$currentUser = $_SESSION['usuario'];

// Validate parameters
$review_id = $_GET['id'] ?? null;
$article_id = $_GET['articulo'] ?? null;

if (!$review_id || !$article_id) {
    die("Parámetros inválidos.");
}

// Check authorization: author or reviewer
$stmt = $pdo->prepare("
    SELECT 1 FROM articulo_autor WHERE ID_articulo = ? AND RUT_autor = ?
    UNION
    SELECT 1 FROM articulo_revisor WHERE ID_articulo = ? AND RUT_revisor = ?
");
$stmt->execute([$article_id, $currentUser, $article_id, $currentUser]);

if ($stmt->rowCount() === 0) {
    die("Acceso denegado. No estás autorizado para ver esta evaluación.");
}

// Fetch article title
$stmt = $pdo->prepare("SELECT titulo FROM articulo WHERE ID_articulo = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if (!$article) {
    die("Artículo no encontrado.");
}

echo "<h1>Evaluación del artículo: " . htmlspecialchars($article['titulo']) . "</h1>";

// Fetch specific review
$stmt = $pdo->prepare("
    SELECT e.*
    FROM evaluacion e
    JOIN revision_articulo ra ON e.ID_revision = ra.ID_revision
    WHERE ra.ID_articulo = ? AND e.ID_revision = ?
");
$stmt->execute([$article_id, $review_id]);
$review = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$review) {
    echo "<p>La evaluación no existe o no corresponde a este artículo.</p>";
} else {
    echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:15px;'>";
    echo "<h3>" . htmlspecialchars($review['nombre_revision']) . "</h3>";
    echo "<p><strong>Calidad técnica:</strong> " . $review['calidad_tecnica'] . "/5</p>";
    echo "<p><strong>Originalidad:</strong> " . $review['originalidad'] . "/5</p>";
    echo "<p><strong>Valoración global:</strong> " . $review['valoracionGlobal'] . "/5</p>";
    echo "<p><strong>Argumentos:</strong><br>" . nl2br(htmlspecialchars($review['argumentos'])) . "</p>";
    if (!empty($review['comentario'])) {
        echo "<p><strong>Comentario adicional:</strong><br>" . nl2br(htmlspecialchars($review['comentario'])) . "</p>";
    }
    echo "</div>";
}

echo "<p><a href='?page=review_article'>Volver a tus revisiones</a></p>";
?>