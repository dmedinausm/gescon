<?php
$host = 'localhost';
$user = 'root';
$password = ''; // SIN contraseña
$dbname = 'gescon';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "conectado";
}
?>
