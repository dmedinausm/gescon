<?php

if($_SESSION['tipo_usuario'] == 'J') {$tipo_usuario = 'Jefe Revisor';}
else if($_SESSION['tipo_usuario'] == 'R'){$tipo_usuario = 'Revisor';}
else if($_SESSION['tipo_usuario'] == 'A'){$tipo_usuario = 'Autor';}
else {$tipo_usuario = 'Usuario';}

?>