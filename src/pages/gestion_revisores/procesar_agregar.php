<?php
require_once 'config\db.php';
if (!empty($_POST['ruts'])) {
    foreach ($_POST['ruts'] as $rut) {
        $conn->query("UPDATE usuario SET tipo_usuario = 'R' WHERE RUT_usuario = '$rut'");
        $conn->query("REPLACE INTO mensaje_revisor (RUT_usuario, mostrar_mensaje) VALUES ('$rut', 1)");
    }
}
header("Location: ?page=agregar_revisores");