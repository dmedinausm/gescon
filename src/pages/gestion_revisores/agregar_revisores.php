<?php
require_once 'config\db.php';
$autores = $conn->query("SELECT * FROM usuario WHERE tipo_usuario = 'A'");
?>

<a href="?page=main" > Menú</a>
<a href="?page=gestion_revisores" > Comité revisores</a>

<h2>Agregar Revisores</h2>
<form action="?page=procesar_agregar" method="post">
    <div style="height: 300px; overflow-y: scroll;">
        <?php while ($autor = $autores->fetch_assoc()): ?>
            <label>
                <input type="checkbox" name="ruts[]" value="<?= $autor['RUT_usuario'] ?>">
                <?= $autor['nombre'] ?> - <?= $autor['email'] ?>
            </label><br>
        <?php endwhile; ?>
    </div>
    <button type="submit">Agregar como revisores</button>
</form>
