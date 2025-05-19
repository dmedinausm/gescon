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

// Check if user is an author or reviewer of the article
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

// Fetch evaluations
$stmt = $pdo->prepare("
    SELECT e.*
    FROM evaluacion e
    JOIN revision_articulo ra ON e.ID_revision = ra.ID_revision
    WHERE ra.ID_articulo = ?
");
$stmt->execute([$article_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Reseñas</title>
    <style>
        /* review_detail.css */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f7f8;
    margin: 0;
    padding: 40px;
    display: flex;
    justify-content: center;
}

.container {
    width: 100%;
    max-width: 1000px;
    background-color: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

h1 {
    color: #004080;
    margin-bottom: 30px;
}

.review-box {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background-color: #f0f4f9;
}

.review-box h3 {
    color: #003366;
    margin-top: 0;
    margin-bottom: 10px;
}

.review-box p {
    margin: 8px 0;
    color: #333;
    line-height: 1.5;
}

.back-link {
    display: inline-block;
    margin-top: 30px;
    text-decoration: none;
    color: white;
    background-color: #004080;
    padding: 10px 15px;
    border-radius: 6px;
    font-size: 14px;
    transition: background-color 0.2s ease;
}

.back-link:hover {
    background-color: #003366;
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Reseñas para el artículo: <?= htmlspecialchars($article['titulo']) ?></h1>

        <?php if (!$reviews): ?>
            <p>No hay reseñas para este artículo.</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-box">
                    <h3><?= htmlspecialchars($review['nombre_revision']) ?></h3>
                    <p><strong>Calidad técnica:</strong> <?= $review['calidad_tecnica'] ?>/5</p>
                    <p><strong>Originalidad:</strong> <?= $review['originalidad'] ?>/5</p>
                    <p><strong>Valoración global:</strong> <?= $review['valoracionGlobal'] ?>/5</p>
                    <p><strong>Argumentos:</strong><br><?= nl2br(htmlspecialchars($review['argumentos'])) ?></p>
                    <?php if (!empty($review['comentario'])): ?>
                        <p><strong>Comentario:</strong><br><?= nl2br(htmlspecialchars($review['comentario'])) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <a class="back-link" href="?page=review_list&id=<?= $article_id ?>">Volver a la lista de evaluaciones</a>
    </div>
</body>
</html>
