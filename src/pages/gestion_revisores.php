<?php
session_start();

include 'config\db.php';
include 'lib.php';
// Verifica si el usuario está logueado y tiene el rol adecuado

if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'J') {
    // Opcional: puedes redirigir a una página de error o login
    header("Location: ?page=acceso_denegado");
    exit();
}

// Crear o actualizar revisor
if (isset($_POST['guardar'])) {
    $rut = $_POST['RUT_usuario'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $topicos = $_POST['topicos'] ?? [];

    // Verifica si existe
    $existe = $conn->query("SELECT * FROM usuario WHERE RUT_usuario = '$rut'")->num_rows;

    if ($existe) {
        $conn->query("UPDATE usuario SET nombre='$nombre', email='$email' WHERE RUT_usuario='$rut'");
    } else {
        $conn->query("INSERT INTO usuario (RUT_usuario, nombre, email, tipo_usuario, password_hash) VALUES 
        ('$rut', '$nombre', '$email', 'R', SHA2('1234', 256))");
    }

     // Elimina y vuelve a insertar los tópicos asignados
    $conn->query("DELETE FROM revisor_topico WHERE RUT_revisor = '$rut'");
    foreach ($topicos as $id_topico) {
        $conn->query("INSERT INTO revisor_topico (RUT_revisor, ID_topico) VALUES ('$rut', $id_topico)");
    }

    header("Location: revisores.php");
    exit();
}
    // Eliminar revisor
if (isset($_GET['eliminar'])) {
    $rut = $_GET['eliminar'];
    $conn->query("DELETE FROM revisor_topico WHERE RUT_revisor = '$rut'");
    $conn->query("DELETE FROM usuario WHERE RUT_usuario = '$rut'");
    // header("Location: revisores.php");
    // exit();
}

// Cargar tópicos
$topicos = $conn->query("SELECT * FROM topico")->fetch_all(MYSQLI_ASSOC);

// Si estamos editando
$editando = false;
$revisorEdit = ['RUT_usuario' => '', 'nombre' => '', 'email' => ''];
$topicosSeleccionados = [];
if (isset($_GET['editar'])) {
    $editando = true;
    $rut = $_GET['editar'];
    $res = $conn->query("SELECT * FROM usuario WHERE RUT_usuario = '$rut'");
    $revisorEdit = $res->fetch_assoc();
    $topSel = $conn->query("SELECT ID_topico FROM revisor_topico WHERE RUT_revisor = '$rut'");
    while ($row = $topSel->fetch_assoc()) {
        $topicosSeleccionados[] = $row['ID_topico'];
    }
}
?>
?>



<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>gestion_revisores</title>
    </head>
    <body>
        <h1>GESCON</h1>
        <h2><?php echo htmlspecialchars($_SESSION['nombre']); ?> </h2> 
        <p>Tu rol es:  <?php echo $tipo_usuario ?>. </p>
    
                
        <ul>
        <li><a href="perfil.php">Perfil</a></li>

        <?php if ($_SESSION['tipo_usuario'] === 'J'): ?>
        <li><a href="?page=main">Página Principal</a></li>
        <li><a href="?page=gestion_revisores">Comité de Revisores</a></li>
        <li><a href="?page=asignar_articulos">Asignar Articulos</a></li>
        <?php endif; ?>
        </ul>
        
  
<h2><?php echo $editando ? "Editar Revisor" : "Nuevo Revisor"; ?></h2>
<form method="POST">
    <input type="text" name="RUT_usuario" placeholder="RUT" value="<?= $revisorEdit['RUT_usuario'] ?>" <?= $editando ? "readonly" : "" ?> required><br>
    <input type="text" name="nombre" placeholder="Nombre" value="<?= $revisorEdit['nombre'] ?>" required><br>
    <input type="email" name="email" placeholder="Email" value="<?= $revisorEdit['email'] ?>" required><br>
    
    <label>Tópicos:</label><br>
    <?php foreach ($topicos as $t): ?>
        <label>
            <input type="checkbox" name="topicos[]" value="<?= $t['ID_topico'] ?>" 
            <?= in_array($t['ID_topico'], $topicosSeleccionados) ? 'checked' : '' ?>>
            <?= $t['nombre_topico'] ?>
        </label><br>
    <?php endforeach; ?>

    <input type="submit" name="guardar" value="Guardar">

</form>


<h2>Lista de Autores</h2>
<table border="1">
    <tr>
        <th>RUT</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Tópicos</th>
        <th>Acciones</th>
    </tr>
    <?php
    $res = $conn->query("SELECT * FROM usuario WHERE tipo_usuario = 'A'");
    while ($row = $res->fetch_assoc()):
        $rut = $row['RUT_usuario'];
        $topicos = $conn->query("SELECT t.nombre_topico FROM revisor_topico rt
                                     JOIN topico t ON rt.ID_topico = t.ID_topico
                                     WHERE rt.RUT_revisor = '$rut'");
        $nombresTopicos = array_column($topicos->fetch_all(MYSQLI_ASSOC), 'nombre_topico');
    ?>
        <tr>
            <td><?= $rut ?></td>
            <td><?= $row['nombre'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= implode(", ", $nombresTopicos) ?></td>
            <td>
                <a href="revisores.php?editar=<?= $rut ?>">Editar</a> |
                <a href="revisores.php?eliminar=<?= $rut ?>" onclick="return confirm('¿Eliminar revisor?')">Eliminar</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>


<hr>



<h2>Lista de Revisores</h2>
<table border="1">
    <tr>
        <th>RUT</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Tópicos</th>
        <th>Acciones</th>
    </tr>
    <?php
    $res = $conn->query("SELECT * FROM usuario WHERE tipo_usuario = 'R'");
    while ($row = $res->fetch_assoc()):
        $rut = $row['RUT_usuario'];
        $topicos = $conn->query("SELECT t.nombre_topico FROM revisor_topico rt
                                     JOIN topico t ON rt.ID_topico = t.ID_topico
                                     WHERE rt.RUT_revisor = '$rut'");
        $nombresTopicos = array_column($topicos->fetch_all(MYSQLI_ASSOC), 'nombre_topico');
    ?>
        <tr>
            <td><?= $rut ?></td>
            <td><?= $row['nombre'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= implode(", ", $nombresTopicos) ?></td>
            <td>
                <a href="revisores.php?editar=<?= $rut ?>">Editar</a> |
                <a href="revisores.php?eliminar=<?= $rut ?>" onclick="return confirm('¿Eliminar revisor?')">Eliminar</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
    </body>
</html>

