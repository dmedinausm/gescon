<?php
require_once 'config/db.php';

$rut = $_GET['id']; // RUT del revisor

// Obtener información del revisor
$revisor = $conn->query("SELECT nombre FROM usuario WHERE RUT_usuario = '$rut'")->fetch_assoc();

// Obtener tópicos del revisor
$topicos_res = $conn->query("
    SELECT t.nombre_topico 
    FROM revisor_topico rt 
    JOIN topico t ON rt.ID_topico = t.ID_topico
    WHERE rt.RUT_revisor = '$rut'
");
$topicos = [];
while ($row = $topicos_res->fetch_assoc()) {
    $topicos[] = $row['nombre_topico'];
}

// Obtener artículos asignados al revisor
$asignados_res = $conn->query("
    SELECT a.ID_articulo, a.titulo 
    FROM articulo a
    JOIN articulo_revisor ar ON a.ID_articulo = ar.ID_articulo
    WHERE ar.RUT_revisor = '$rut'
");

// Obtener artículos disponibles para asignar (por tópicos y sin que sea autor)
$articulos_disponibles = $conn->query("
    SELECT DISTINCT a.ID_articulo, a.titulo 
    FROM articulo a
    JOIN articulo_topico at ON a.ID_articulo = at.ID_articulo
    JOIN revisor_topico rt ON at.ID_topico = rt.ID_topico
    WHERE rt.RUT_revisor = '$rut'
    AND a.ID_articulo NOT IN (
        SELECT ID_articulo FROM articulo_revisor WHERE RUT_revisor = '$rut'
    )
    AND a.ID_articulo NOT IN (
        SELECT ID_articulo FROM articulo_autor WHERE RUT_autor = '$rut'
    )
");
?>
<link rel="stylesheet" href="src/styles/revisores/detalle.css">

<div class="container">
    <a href="?page=gestion_revisores" class="back-link">← Volver a miembros del comité</a>

    <h2>Revisor: <?= htmlspecialchars($revisor['nombre']) ?> <span class="rut">(<?= htmlspecialchars($rut) ?>)</span></h2>

    <p><strong>Especialidades:</strong> <?= implode(', ', $topicos) ?: '<em>Ninguno</em>' ?></p>

    <section class="section">
        <h3>Artículos Asignados</h3>
        <?php if ($asignados_res->num_rows > 0): ?>
            <ul class="assigned-list">
                <?php while ($a = $asignados_res->fetch_assoc()): ?>
                    <li>
                        <span><?= $a['ID_articulo'] ?> - <?= htmlspecialchars($a['titulo']) ?></span>
                        <form method="post" action="index.php?page=quitar_articulo_revisor" class="inline-form">
                            <input type="hidden" name="rut" value="<?= $rut ?>">
                            <input type="hidden" name="id_articulo" value="<?= $a['ID_articulo'] ?>">
                            <button type="submit" class="remove-btn">Quitar</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="empty">Este revisor no tiene artículos asignados.</p>
        <?php endif; ?>
    </section>

    <section class="section">
        <h3>Asignar Nuevo Artículo</h3>
        <form method="post" action="index.php?page=asignar_articulo_revisor" class="assign-form">
            <input type="hidden" name="rut" value="<?= $rut ?>">
            <select name="id_articulo" required>
                <?php while ($row = $articulos_disponibles->fetch_assoc()): ?>
                    <option value="<?= $row['ID_articulo'] ?>">
                        <?= $row['ID_articulo'] ?> - <?= htmlspecialchars($row['titulo']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Asignar</button>
        </form>
    </section>
</div>
