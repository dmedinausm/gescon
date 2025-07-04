<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=gescon", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check article ID
if (!isset($_GET['id'])) {
    die("ID de artículo no proporcionado.");
}
$article_id = $_GET['id'];

// Verify user is an author of this article
$stmt = $pdo->prepare("SELECT RUT_autor FROM articulo_autor WHERE ID_articulo = ?");
$stmt->execute([$article_id]);
$author_ruts = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario'], $author_ruts)) {
    die("Acceso denegado. Solo los autores pueden editar este artículo.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Delete article
        try {
            $pdo->beginTransaction();

            $pdo->prepare("DELETE FROM articulo_autor WHERE ID_articulo = ?")->execute([$article_id]);
            $stmt = $pdo->prepare("DELETE FROM articulo_topico WHERE ID_articulo = ?");
            $stmt->execute([$article_id]);
            $pdo->prepare("DELETE FROM articulo WHERE ID_articulo = ?")->execute([$article_id]);

            $pdo->commit();
            header("Location: ?page=view_article");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "<p>Error al eliminar el artículo: {$e->getMessage()}</p>";
        }
    } else {
        // Update article
        $titulo = trim($_POST['titulo']);
        $resumen = trim($_POST['resumen']);
        if (empty($_POST['topicos'])) {
            echo "<p style='color:red;'>Debes seleccionar al menos un tópico.</p>";
            return;
        }
        $topico_ids = isset($_POST['topicos']) ? $_POST['topicos'] : [];
        $new_ruts = array_filter($_POST['autor_ruts']);

        try {
            $pdo->beginTransaction();

            $pdo->prepare("UPDATE articulo SET titulo = ?, resumen = ? WHERE ID_articulo = ?")
                ->execute([$titulo, $resumen, $article_id]);

            $pdo->prepare("DELETE FROM articulo_topico WHERE ID_articulo = ?")->execute([$article_id]);    
            $stmt = $pdo->prepare("INSERT INTO articulo_topico (ID_articulo, ID_topico) VALUES (?, ?)");
            foreach ($topico_ids as $topico_id) {
                $stmt->execute([$article_id, $topico_id]);
            }

            // Replace authors
            $pdo->prepare("DELETE FROM articulo_autor WHERE ID_articulo = ?")->execute([$article_id]);
            $stmtInsert = $pdo->prepare("INSERT INTO articulo_autor (ID_articulo, RUT_autor, is_contact) VALUES (?, ?, ?)");
            foreach ($new_ruts as $i => $rut) {
                $stmtInsert->execute([$article_id, $rut, $i === 0 ? 1 : 0]);
            }

            $pdo->commit();
            echo "<p>Artículo actualizado correctamente.</p>";
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "<p>Error al actualizar el artículo: {$e->getMessage()}</p>";
        }
    }
}

// Fetch current article data
$stmt = $pdo->prepare("
    SELECT a.titulo, a.resumen, t.ID_topico 
    FROM articulo a
    JOIN articulo_topico t ON a.ID_articulo = t.ID_articulo
    WHERE a.ID_articulo = ?
");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

$stmt = $pdo->query("SELECT ID_topico, nombre_topico FROM topico");
$topics = $stmt->fetchAll();

$stmt = $pdo->prepare("
    SELECT RUT_autor FROM articulo_autor
    WHERE ID_articulo = ?
    ORDER BY is_contact DESC
");
$stmt->execute([$article_id]);
$current_authors = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html>
<head><title>Editar Artículo</title></head>
<body>

<h1>Editar Artículo</h1>

<form method="post">
    <input type="text" name="titulo" value="<?= htmlspecialchars($article['titulo']) ?>" required><br>
    <textarea name="resumen" required><?= htmlspecialchars($article['resumen']) ?></textarea><br>

    <label>Seleccione uno o más tópicos:</label><br>
    <?php
    // Get all available topics
    $stmt = $pdo->query("SELECT ID_topico, nombre_topico FROM topico");
    $all_topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get topics currently assigned to this article
    $stmt = $pdo->prepare("SELECT ID_topico FROM articulo_topico WHERE ID_articulo = ?");
    $stmt->execute([$article_id]);
    $current_topic_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($all_topics as $topic) {
        $checked = in_array($topic['ID_topico'], $current_topic_ids) ? 'checked' : '';
        echo "<label><input type='checkbox' name='topicos[]' value='{$topic['ID_topico']}' $checked> {$topic['nombre_topico']}</label><br>";
    }
    ?>
    <br>

    <p>Autores (máx 3, el primero es el contacto):</p>
    <?php for ($i = 0; $i < 3; $i++): ?>
        <input type="text" name="autor_ruts[]" value="<?= $current_authors[$i] ?? '' ?>" <?= $i === 0 ? 'required' : '' ?>><br>
    <?php endfor; ?>

    <button type="submit">Guardar Cambios</button>
    <button type="submit" name="delete" onclick="return confirm('¿Está seguro que desea eliminar este artículo?')">Eliminar Artículo</button>
</form>

<p><a href="?page=view_article">Volver a la lista de artículos</a></p>

</body>
</html>