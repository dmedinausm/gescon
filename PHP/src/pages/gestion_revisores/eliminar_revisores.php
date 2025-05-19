<?php
require_once 'config\db.php';
$rut = $_GET['rut'];

// Cambiar tipo_usuario a 'A'
$conn->query("UPDATE usuario SET tipo_usuario = 'A' WHERE RUT_usuario = '$rut'");

// Borrar sus tópicos
$conn->query("DELETE FROM revisor_topico WHERE RUT_revisor = '$rut'");

header("Location: ?page=gestion_revisores");
?>