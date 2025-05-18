<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=gescon", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_GET['id'])) {
    die("ID de artículo no proporcionado.");
}
$article_id = $_GET['id'];

// Verificar si el usuario está logueado y es revisor asignado
if (!isset($_SESSION['usuario'])) {
    die("Acceso denegado. Debes iniciar sesión.");
}

$current_user = $_SESSION['usuario'];
$stmt = $pdo->prepare("SELECT COUNT(*) FROM articulo_revisor WHERE ID_articulo = ? AND RUT_revisor = ?");
$stmt->execute([$article_id, $current_user]);

if ($stmt->fetchColumn() == 0) {
    die("No estás autorizado para revisar este artículo.");
}

// Procesar formulario de evaluación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_revision']);
    $calidad = (int)$_POST['calidad_tecnica'];
    $originalidad = (int)$_POST['originalidad'];
    $global = (int)$_POST['valoracionGlobal'];
    $argumentos = trim($_POST['argumentos']);
    $comentario = trim($_POST['comentario']);

    try {
        $pdo->beginTransaction();

        // Insertar en evaluacion
        $stmt = $pdo->prepare("INSERT INTO evaluacion (nombre_revision, calidad_tecnica, originalidad, valoracionGlobal, argumentos, comentario)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $calidad, $originalidad, $global, $argumentos, $comentario]);

        $revision_id = $pdo->lastInsertId();

        // Relacionar evaluación con artículo
        $stmt = $pdo->prepare("INSERT INTO revision_articulo (ID_articulo, ID_revision) VALUES (?, ?)");
        $stmt->execute([$article_id, $revision_id]);

        $pdo->commit();

        echo "<p>Evaluación registrada exitosamente.</p><a href='?page=view_reviews'>Ver mis evaluaciones</a>";
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "<p>Error al registrar la evaluación: {$e->getMessage()}</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agregar Evaluación</title>
</head>
<body>
<h1>Evaluar Artículo</h1>
<form method="post">
    <label>Nombre de la revisión:</label><br>
    <input type="text" name="nombre_revision" required><br><br>

    <label>Calidad Técnica (1-5):</label><br>
    <input type="number" name="calidad_tecnica" min="1" max="5" required><br><br>

    <label>Originalidad (1-5):</label><br>
    <input type="number" name="originalidad" min="1" max="5" required><br><br>

    <label>Valoración Global (1-5):</label><br>
    <input type="number" name="valoracionGlobal" min="1" max="5" required><br><br>

    <label>Argumentos:</label><br>
    <textarea name="argumentos" maxlength="300" required></textarea><br><br>

    <label>Comentario adicional (opcional):</label><br>
    <textarea name="comentario" maxlength="300"></textarea><br><br>

    <button type="submit">Enviar Evaluación</button>
</form>
<p><a href='?page=view_articles'>Volver a Artículos</a></p>
</body>
</html>
