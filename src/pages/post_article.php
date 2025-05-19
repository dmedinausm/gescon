<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Subir Artículo</title>
    <link rel="stylesheet" href="src/styles/article/post_article.css">
</head>
<body>
<div class="container">
    <h1>Subir un nuevo artículo</h1>
    <p>Ingrese los detalles del artículo.</p>

    <?php
    session_start();
    $pdo = new PDO("mysql:host=localhost; dbname=gescon", "root", "");
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        try {
        $titulo = trim($_POST['titulo']);
        $resumen = trim($_POST['resumen']);
        $topico_ids = isset($_POST['topicos']) ? $_POST['topicos'] : [];
        $autor_nombres = $_POST['autor_nombres'];
        $autor_emails = $_POST['autor_emails'];
        $autor_ruts = [];

        if (empty($topico_ids)) {
            throw new Exception("Debe seleccionar al menos un tópico.");
        }

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

    
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();

            // Insert artículo
            $stmt = $pdo->prepare("INSERT INTO articulo (titulo, fecha_envio, resumen) VALUES (?, CURDATE(), ?)");
            $stmt->execute([$titulo, $resumen]);
            $articulo_id = $pdo->lastInsertId();

            // Insert tópicos
            $stmt = $pdo->prepare("INSERT INTO articulo_topico (ID_articulo, ID_topico) VALUES (?, ?)");
            foreach ($topico_ids as $topico_id) {
                $stmt->execute([$articulo_id, $topico_id]);
            }

            // Insert autores
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
    catch (Exception $e) {
        echo "<p style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
<p><a href="?page=view_article">Volver a la lista de artículos</a></p>
