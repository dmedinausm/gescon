<?php
require_once 'config\db.php';
if (!empty($_POST['ruts'])) {
    foreach ($_POST['ruts'] as $rut) {
        $conn->query("UPDATE usuario SET tipo_usuario = 'R' WHERE RUT_usuario = '$rut'");
    }
}
header("Location: ?page=agregar_revisores");