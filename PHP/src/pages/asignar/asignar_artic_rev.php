<?php 
require_once 'config/db.php';

$sql = "SELECT * FROM vista_articulo_info";
$articulos = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Revisores</title>
    <link rel="stylesheet" href="PHP/src/styles/asignar/asignar_artic_rev.css">
</head>
<body>

<div class="container">
    <a href="?page=main" class="back-link">← Ir al Menú</a>
    <h2>Asignar Revisores a Artículos</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID Artículo</th>
                    <th>Título</th>
                    <th>Autores</th>
                    <th>Tópicos</th>
                    <th>Revisores</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($art = $articulos->fetch_assoc()): ?>
                    <?php
                    $id = $art['ID_articulo'];
                    $autores = $art['autores'] ?: 'Sin autores';
                    $topicos = $art['topicos'] ?: 'No definidos';

                    // Procesar revisores
                    $revisores_raw = $art['revisores'];
                    $revisores = [];
                    if ($revisores_raw) {
                        foreach (explode(';;', $revisores_raw) as $rev) {
                            list($nombre, $rut) = explode('||', $rev);
                            $revisores[] = "$nombre <a class='quitar' href='?page=quitar_revisor&id=$id&rut=$rut' onclick='return confirm(\"¿Quitar revisor?\")'>[Quitar]</a>";
                        }
                    }

                    // Obtener cantidad de revisores con la función
                    $revisorCountRes = $conn->query("SELECT contar_revisores($id) AS cantidad");
                    $revisorCount = $revisorCountRes->fetch_assoc()['cantidad'];
                    ?>
                    <tr class="<?= $revisorCount < 2 ? 'pocos-revisores' : '' ?>">
                        <td><?= $id ?></td>
                        <td><?= htmlspecialchars($art['titulo']) ?></td>
                        <td><?= htmlspecialchars($autores) ?></td>
                        <td><?= htmlspecialchars($topicos) ?></td>
                        <td><?= implode('<br>', $revisores) ?: 'Sin revisores' ?></td>
                        <td><a class="asignar-btn" href="?page=asignar_revisor&id=<?= $id ?>">Asignar</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
