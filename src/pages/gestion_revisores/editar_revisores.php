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
<form method="post">
    <h2>Editar tópicos para <?= $rut ?></h2>
    <?php while ($topico = $topicos->fetch_assoc()): ?>
        <label>
            <input type="checkbox" name="topicos[]" value="<?= $topico['ID_topico'] ?>"
                <?= in_array($topico['ID_topico'], $asignados) ? 'checked' : '' ?>>
            <?= $topico['nombre_topico'] ?>
        </label><br>
    <?php endwhile; ?>
    <button type="submit">Guardar</button>
</form>