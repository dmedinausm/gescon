<?php 

require_once 'config\db.php';
$rut = $_GET['rut'];

//obtener todos los tópicos
$topicos = $conn->query("SELECT * FROM topico");

//topicos actuales del revisor
$asignados = [];
$res = $conn->query("SELECT ID_topico FROM revisor_topico WHERE RUT_revisor = '$rut'");
while($r = $res->fetch_assoc()) $asignados[] = $r['ID_topico'];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $conn->query("DELETE FROM revisor_topico WHERE RUT_revisor = '$rut'");
    if(!empty($_POST['topicos'])) {
        foreach($_POST['topicos'] as $id_topico) {
            $conn->query("INSERT INTO revisor_topico (RUT_revisor, ID_topico) VALUES ('$rut', $id_topico)");
        }
    }
    header("Location: ?page=gestion_revisores");
}
?>
<link rel="stylesheet" href="PHP/src/styles/revisores/detalle.css">

<div class="container">
    <a href="?page=gestion_revisores" class="back-link">← Volver a miembros del comité</a>

    <h2>Editar Tópicos para <span class="rut"><?= htmlspecialchars($rut) ?></span></h2>

    <form method="post" class="assign-form">
        <div class="checkbox-list">
            <?php while ($topico = $topicos->fetch_assoc()): ?>
                <label class="checkbox-item">
                    <input type="checkbox" name="topicos[]" value="<?= $topico['ID_topico'] ?>"
                        <?= in_array($topico['ID_topico'], $asignados) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($topico['nombre_topico']) ?>
                </label>
            <?php endwhile; ?>
        </div>
        <button type="submit">Guardar</button>
    </form>
</div>
