<?php
require_once 'config\db.php';
$autores = $conn->query("SELECT * FROM usuario WHERE tipo_usuario = 'A'");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Revisores</title>
    <link rel="stylesheet" href="src/styles/revisores/agregar_revisores.css">
</head>
<body>

<div class="container">
    <nav class="nav-links">
        <!-- <a href="?page=main">← Menú</a> -->
        <a href="?page=gestion_revisores"> ← Volver</a>
    </nav>

    <h2>Agregar Revisores</h2>

    <form action="?page=procesar_agregar" method="post" class="form-box">
        <div class="checkbox-list">
            <?php while ($autor = $autores->fetch_assoc()): ?>
                <label class="checkbox-item">
                    <input type="checkbox" name="ruts[]" value="<?= $autor['RUT_usuario'] ?>">
                    <?= htmlspecialchars($autor['nombre']) ?> - <?= htmlspecialchars($autor['email']) ?>
                </label>
            <?php endwhile; ?>
        </div>
        <button type="submit" class="submit-btn">Agregar como revisores</button>
    </form>
</div>

</body>
</html>
