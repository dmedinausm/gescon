<?php
require_once 'config/db.php';

$id_articulo = intval($_GET['id']);

// 1. Vamos a obtener el rut de los autores para que no puedan ser revisores de su propio articulo
$autores_res = $conn->query("SELECT RUT_autor FROM articulo_autor WHERE ID_articulo = $id_articulo");
$autores = [];
while ($a = $autores_res->fetch_assoc()) {
    $autores[] = $a['RUT_autor'];
}
//implode lo que hace es concatenar dos strings
//entonces de esta forma, separamos todos los ruts con ,
$autores_placeholders = count($autores) > 0 ? ("'" . implode("','", $autores) . "'") : "''";

// 2. Obtener los ID_topico del artículo, para que en la tabla se trabaje con todos los topicos de los articulos
$res_topico_ids = $conn->query("SELECT ID_topico FROM articulo_topico WHERE ID_articulo = $id_articulo");
$topico_ids = [];
while ($r = $res_topico_ids->fetch_assoc()) {
    $topico_ids[] = $r['ID_topico'];
}

// 3. Buscar revisores que coincidan con esos tópicos Y que no sean autores, entonces 
// con que un topico calce, ya es suficiente para que sea revisor 
$revisores = [];
if (count($topico_ids) > 0) {
    $topicos_placeholder = implode(',', $topico_ids);

    //filtrado de revisores 
    $revisores_sql = "
        SELECT DISTINCT u.RUT_usuario, u.nombre 
        FROM usuario u
        JOIN revisor_topico rt ON u.RUT_usuario = rt.RUT_revisor
        WHERE u.tipo_usuario = 'R'
        AND rt.ID_topico IN ($topicos_placeholder)
        AND u.RUT_usuario NOT IN ($autores_placeholders)
    ";
    $revisores = $conn->query($revisores_sql);
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Revisor</title>
    <link rel="stylesheet" href="src/styles/asignar/asignar.css">
</head>
<body>

    <div class="container">
        <h3>Asignar Revisor al Artículo #<?= $id_articulo ?></h3>

        <?php if (!empty($topico_ids)): ?>
            <form method="post" action="index.php?page=procesar_asignar_revisor" class="assign-form">
                <input type="hidden" name="id_articulo" value="<?= $id_articulo ?>">
                
                <label for="rut_revisor">Selecciona un revisor:</label>
                <select name="rut_revisor" id="rut_revisor" required>
                    <?php while ($rev = $revisores->fetch_assoc()): ?>
                        <option value="<?= $rev['RUT_usuario'] ?>">
                            <?= $rev['nombre'] ?> (<?= $rev['RUT_usuario'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <button type="submit">Asignar</button>
            </form>
        <?php else: ?>
            <div class="warning">
                <p><strong>Este artículo no tiene tópicos asociados.</strong></p>
                <p>Asóciale al menos un tópico para poder asignar revisores.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>

