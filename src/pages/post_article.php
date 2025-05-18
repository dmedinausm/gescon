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
    
    <label>Seleccione uno o más tópicos:</label><br>
    <?php
    $pdo = new PDO("mysql:host=localhost; dbname=gescon", "root", "");
    $stmt = $pdo->query("SELECT ID_topico, nombre_topico FROM topico");
    while ($row = $stmt->fetch()) {
        echo "<label><input type='checkbox' name='topicos[]' value='{$row['ID_topico']}'> {$row['nombre_topico']}</label><br>";
    }
    ?>
    <br>

    <p>Ingrese hasta 3 autores (el primero será el autor de contacto):</p>
    <?php for ($i = 0; $i < 3; $i++): ?>
    <input type="text" name="autor_nombres[]" placeholder="Nombre del autor <?= $i + 1 ?>" <?= $i == 0 ? 'required' : '' ?>><br>
    <input type="email" name="autor_emails[]" placeholder="Email del autor <?= $i + 1 ?>" <?= $i == 0 ? 'required' : '' ?>><br><br>
    <?php endfor; ?>

    <button type="submit">Subir artículo</button>
</form>

</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $resumen = trim($_POST['resumen']);
    $topico_ids = isset($_POST['topicos']) ? $_POST['topicos'] : [];
    if (empty($topico_ids)) {
        throw new Exception("Debe seleccionar al menos un tópico.");
    }
    $autor_nombres = $_POST['autor_nombres'];
    $autor_emails = $_POST['autor_emails'];
    $autor_ruts = [];

    for ($i = 0; $i < count($autor_nombres); $i++) {
        $nombre = trim($autor_nombres[$i]);
        $email = trim($autor_emails[$i]);

        if ($nombre && $email) {
            // Find RUT based on name and email
            $stmt = $pdo->prepare("SELECT RUT_usuario FROM usuario WHERE nombre = ? AND email = ?");
            $stmt->execute([$nombre, $email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $autor_ruts[] = $row['RUT_usuario'];
            } else {
                throw new Exception("Autor no encontrado: $nombre <$email>");
            }
        }
    }

    try {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();

        // Insert into articulo
        $stmt = $pdo->prepare("INSERT INTO articulo (titulo, fecha_envio, resumen) VALUES (?, CURDATE(), ?)");
        $stmt->execute([$titulo, $resumen]);
        $articulo_id = $pdo->lastInsertId();

        // Insert into articulo_topico
        $stmt = $pdo->prepare("INSERT INTO articulo_topico (ID_articulo, ID_topico) VALUES (?, ?)");
        foreach ($topico_ids as $topico_id) {
            $stmt->execute([$articulo_id, $topico_id]);
        }

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
// INSERT INTO topico (nombre_topico) VALUES ('Ingeniería'), ('Ciencia'), ('Salud'), ('Comedia'), ('Seguridad'), ('Celebridades'), ('Deportes');
?>
<p><a href="?page=view_article">Volver a la lista de artículos</a></p>
