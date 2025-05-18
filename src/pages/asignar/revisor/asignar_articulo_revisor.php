<?php
require_once 'config/db.php';

$rut = $_POST['rut'];
$id = intval($_POST['id_articulo']);

$conn->query("INSERT INTO articulo_revisor (RUT_revisor, ID_articulo) VALUES ('$rut', $id)");

header("Location: index.php?page=detalle_revisor&id=$rut");
exit;
