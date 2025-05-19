<?php
require_once 'config/db.php';

$rut = $_POST['rut'];
$id = intval($_POST['id_articulo']);

$conn->query("DELETE FROM articulo_revisor WHERE RUT_revisor = '$rut' AND ID_articulo = $id");

header("Location: index.php?page=detalle_revisor&id=$rut");
exit;
