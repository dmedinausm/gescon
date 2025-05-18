<?php
require_once 'config/db.php';

$id = intval($_POST['id_articulo']);
$rut = $conn->real_escape_string($_POST['rut_revisor']);

// Verificar que no sea autor
$check = $conn->query("SELECT 1 FROM articulo_autor WHERE ID_articulo = $id AND RUT_autor = '$rut'");
if ($check->num_rows > 0) {
    echo "Error: El revisor es autor del artículo.";
    exit;
}

// Insertar
$conn->query("INSERT IGNORE INTO articulo_revisor (ID_articulo, RUT_revisor) VALUES ($id, '$rut')");
header("Location: ?page=asignar_artic_rev");
?>