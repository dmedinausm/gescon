<!DOCTYPE html>
<html>
<head>
    <title>Subir Artículo</title>
</head>
<body>
<h1>Subir un nuevo artículo</h1>
<p>Ingrese los detalles del artículo.</p>

<form action="?page=post_article" method="post">
    <input type="text" name="titulo" placeholder="Título del artículo" required><br>
    <textarea name="resumen" placeholder="Resumen del artículo" required></textarea><br>
    
    <label for="topico">Seleccione un tópico:</label>
    <select name="topico" required>
        <?php
        $pdo = new PDO("mysql:host=localhost; dbname=gescon", "root", "");
        $stmt = $pdo->query("SELECT ID_topico, nombre_topico FROM topico");
        while ($row = $stmt->fetch()) {
            echo "<option value='{$row['ID_topico']}'>{$row['nombre_topico']}</option>";
        }
        ?>
    </select><br><br>

    <p>Ingrese hasta 3 RUTs de autores (el primero será el autor de contacto):</p>
    <input type="text" name="autor_ruts[]" placeholder="RUT Autor 1" required><br>
    <input type="text" name="autor_ruts[]" placeholder="RUT Autor 2 (opcional)"><br>
    <input type="text" name="autor_ruts[]" placeholder="RUT Autor 3 (opcional)"><br><br>

    <button type="submit">Subir artículo</button>

    <br>
    <a href="?page=main">Ir al menu</a>
</form>

</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $resumen = trim($_POST['resumen']);
    $topico_id = $_POST['topico'];
    $autor_ruts = array_filter($_POST['autor_ruts']); // Remove empty RUTs

    try {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();

        // Insert into articulo
        $stmt = $pdo->prepare("INSERT INTO articulo (titulo, fecha_envio, resumen) VALUES (?, CURDATE(), ?)");
        $stmt->execute([$titulo, $resumen]);
        $articulo_id = $pdo->lastInsertId();

        // Insert into articulo_topico
        $stmt = $pdo->prepare("INSERT INTO articulo_topico (ID_articulo, ID_topico) VALUES (?, ?)");
        $stmt->execute([$articulo_id, $topico_id]);

        // Insert into articulo_autor
        $stmt = $pdo->prepare("INSERT INTO articulo_autor (ID_articulo, RUT_autor, is_contact) VALUES (?, ?, ?)");
        foreach ($autor_ruts as $index => $rut) {
            $contact = ($index === 0) ? 1 : 0;
            $stmt->execute([$articulo_id, $rut, $contact]);
        }

        $pdo->commit();
        echo "<p>Artículo registrado correctamente.</p>";

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "<p>Error al registrar el artículo: " . $e->getMessage() . "</p>";
    }
}
?>

