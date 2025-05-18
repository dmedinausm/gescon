<?php 
session_start();
require_once 'config/db.php';
if(!isset($_SESSION['usuario'])) {
    header("Location: ?page=login");
    header("Location: ?page=post_article");
    header("Location: ?page=view_article");
    exit();
}
include 'lib.php';

$rut = $_SESSION['usuario'] ?? null;

if ($rut) {
    // Verificamos si hay un mensaje pendiente
    $res = $conn->query("SELECT mostrar_mensaje FROM mensaje_revisor WHERE RUT_usuario = '$rut'");
    $fila = $res->fetch_assoc();

    if ($fila && $fila['mostrar_mensaje']) {
        echo "<div class='alerta'>⚠️ ¡Has sido promovido a Revisor! Por favor, agrega tu especialidad.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>GESCON - Panel Principal</title>
    <link rel="stylesheet" href="src/styles/main.css">
</head>
<body>
    <header>
        <h1>GESCON</h1>
        <h2>Bienvenido, <?= htmlspecialchars($_SESSION['nombre']); ?> 👋</h2>
        <p>Tu rol es: <?= $tipo_usuario ?>.</p>
        <p class="mensaje-exito">Has iniciado sesión correctamente.</p>
        <nav>
            <ul class="nav-links">
                <li><a href="?page=login">Ir al login</a></li>
                <li><a href="?page=view_article">Ver artículos</a></li>
                <li><a href="?page=adv_search">Búsqueda avanzada</a></li>
                <li><a href="?page=perfil">Perfil</a></li>

                <?php if ($_SESSION['tipo_usuario'] === 'R'): ?>
                    <li><a href="?page=review_article">Área de revisión</a></li>
                <?php endif; ?>

                <?php if ($_SESSION['tipo_usuario'] === 'J'): ?>
                    <li><a href="?page=gestion_revisores">Comité de Revisores</a></li>
                    <li><a href="?page=asignar_artic_rev">Asignar Artículos</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section class="busqueda">
            <h2>Lista de Artículos Subidos</h2>
            <form method="get" action="">
                <input type="hidden" name="page" value="main">
                <input type="text" name="q" placeholder="Buscar..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button type="submit">Buscar</button>
            </form>
        </section>

        <?php
        try {
            $pdo = new PDO("mysql:host=localhost; dbname=gescon", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $search = $_GET['q'] ?? '';
            $params = [];

            $sql = "
                SELECT DISTINCT a.ID_articulo, a.titulo, a.fecha_envio, a.resumen
                FROM articulo a
                JOIN articulo_topico at ON a.ID_articulo = at.ID_articulo
                JOIN topico t ON at.ID_topico = t.ID_topico
                LEFT JOIN articulo_autor aa ON a.ID_articulo = aa.ID_articulo
                LEFT JOIN usuario u ON aa.RUT_autor = u.RUT_usuario
            ";

            if (!empty($search)) {
                $sql .= " WHERE 
                    a.titulo LIKE :q OR 
                    a.resumen LIKE :q OR 
                    t.nombre_topico LIKE :q OR 
                    u.nombre LIKE :q
                ";
                $params['q'] = '%' . $search . '%';
            }

            $sql .= " ORDER BY a.fecha_envio DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($articles) === 0) {
                echo "<p class='alerta'>No hay artículos registrados.</p>";
            } else {
                foreach ($articles as $article) {
                    echo "<section class='articulo'>";
                    echo "<h3>{$article['titulo']}</h3>";
                    echo "<p><strong>Fecha de envío:</strong> {$article['fecha_envio']}</p>";

                    // Obtener tópicos
                    $stmt_topics = $pdo->prepare("
                        SELECT t.nombre_topico
                        FROM articulo_topico at
                        JOIN topico t ON at.ID_topico = t.ID_topico
                        WHERE at.ID_articulo = ?
                    ");
                    $stmt_topics->execute([$article['ID_articulo']]);
                    $topics = $stmt_topics->fetchAll(PDO::FETCH_COLUMN);
                    echo "<p><strong>Tópicos:</strong> " . implode(", ", $topics) . "</p>";

                    echo "<p><strong>Resumen:</strong> {$article['resumen']}</p>";

                    // Obtener autores
                    $stmt2 = $pdo->prepare("
                        SELECT aa.RUT_autor, u.nombre, aa.is_contact
                        FROM articulo_autor aa
                        JOIN usuario u ON aa.RUT_autor = u.RUT_usuario
                        WHERE aa.ID_articulo = ?
                    ");
                    $stmt2->execute([$article['ID_articulo']]);
                    $authors = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                    echo "<p><strong>Autores:</strong><ul>";
                    foreach ($authors as $author) {
                        $contactMark = $author['is_contact'] ? " (autor de contacto)" : "";
                        echo "<li>{$author['nombre']} ({$author['RUT_autor']}){$contactMark}</li>";
                    }
                    echo "</ul></p>";

                    echo "</section>";
                }
            }
        } catch (PDOException $e) {
            echo "<p class='alerta'>Error al obtener los artículos: " . $e->getMessage() . "</p>";
        }
        ?>
    </main>

    <footer>
        <p><a href="?page=login">Cerrar sesión</a></p>
    </footer>
</body>
</html>
