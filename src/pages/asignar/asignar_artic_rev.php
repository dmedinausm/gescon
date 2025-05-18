<?php 
require_once 'config/db.php';

$sql = "SELECT a.ID_articulo, a.titulo FROM articulo a";
$articulos = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Revisores</title>
    <link rel="stylesheet" href="src/styles/asignar/asignar_artic_rev.css">
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

                    // Autores
                    $res = $conn->query("SELECT u.nombre FROM articulo_autor aa JOIN usuario u ON aa.RUT_autor = u.RUT_usuario WHERE ID_articulo = $id");
                    $autores = [];
                    while ($r = $res->fetch_assoc()) $autores[] = $r['nombre'];

                    // Tópicos
                    $res = $conn->query("
                        SELECT t.nombre_topico 
                        FROM articulo_topico at
                        JOIN topico t ON at.ID_topico = t.ID_topico
                        WHERE at.ID_articulo = $id
                    ");
                    $topicos = [];
                    while ($r = $res->fetch_assoc()) $topicos[] = $r['nombre_topico'];

                    // Revisores
                    $res = $conn->query("
                        SELECT u.RUT_usuario, u.nombre 
                        FROM articulo_revisor ar
                        JOIN usuario u ON ar.RUT_revisor = u.RUT_usuario
                        WHERE ar.ID_articulo = $id
                    ");
                    $revisores = [];
                    while ($r = $res->fetch_assoc()) {
                        $rut_revisor = $r['RUT_usuario'];
                        $nombre_revisor = $r['nombre'];
                        $revisores[] = "$nombre_revisor <a class='quitar' href='?page=quitar_revisor&id=$id&rut=$rut_revisor' onclick='return confirm(\"¿Quitar revisor?\")'>[Quitar]</a>";
                    }
                    ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><?= $art['titulo'] ?></td>
                        <td><?= implode(', ', $autores) ?></td>
                        <td><?= implode(', ', $topicos) ?: 'No definidos' ?></td>
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

