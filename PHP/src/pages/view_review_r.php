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

// Check authorization
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Evaluación de Artículo</title>
    <style>
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

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        h2 {
            margin: 10px 0;
            color: #004080;
        }

        .back-button {
            text-decoration: none;
            color: white;
            background-color: #004080;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }

        .back-button:hover {
            background-color: #003366;
        }

        .review-box {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 20px;
            margin-top: 20px;
        }

        .review-box h3 {
            color: #004080;
            margin-top: 0;
        }

        .review-box p {
            margin: 10px 0;
        }

        a {
            color: #004080;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Evaluación del artículo: <?= htmlspecialchars($article['titulo']) ?></h2>
            <a href="?page=review_article" class="back-button">Volver a tus revisiones</a>
        </div>

        <?php
        // Fetch review
        $stmt = $pdo->prepare("
            SELECT e.*
            FROM evaluacion e
            JOIN revision_articulo ra ON e.ID_revision = ra.ID_revision
            WHERE ra.ID_articulo = ? AND e.ID_revision = ?
        ");
        $stmt->execute([$article_id, $review_id]);
        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$review): ?>
            <div class="review-box">
                <p>La evaluación no existe o no corresponde a este artículo.</p>
            </div>
        <?php else: ?>
            <div class="review-box">
                <h3><?= htmlspecialchars($review['nombre_revision']) ?></h3>
                <p><strong>Calidad técnica:</strong> <?= $review['calidad_tecnica'] ?>/5</p>
                <p><strong>Originalidad:</strong> <?= $review['originalidad'] ?>/5</p>
                <p><strong>Valoración global:</strong> <?= $review['valoracionGlobal'] ?>/5</p>
                <p><strong>Argumentos:</strong><br><?= nl2br(htmlspecialchars($review['argumentos'])) ?></p>
                <?php if (!empty($review['comentario'])): ?>
                    <p><strong>Comentario adicional:</strong><br><?= nl2br(htmlspecialchars($review['comentario'])) ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
