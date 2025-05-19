<?php
require_once 'config/db.php';

$id = intval($_GET['id']);
$rut = $conn->real_escape_string($_GET['rut']);

$conn->query("DELETE FROM articulo_revisor WHERE ID_articulo = $id AND RUT_revisor = '$rut'");
header("Location: ?page=asignar_artic_rev");
?>