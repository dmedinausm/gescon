
<?php
session_start();
include 'config\db.php';

$RUT_usuario = $_POST['RUT_usuario'];
$nombre      = $_POST['nombre'];
$email       = $_POST['email'];
$tipo_usuario = $_POST['tipo_usuario'];
$password    = $_POST['password'];

// Validar campos
if (!$password) {
    die("Debes ingresar tu contraseña para confirmar.");
}

// Verificar la contraseña (esto asume que tienes la contraseña hasheada en la base de datos)
$stmt = $pdo->prepare("SELECT password FROM usuarios WHERE RUT_usuario = ?");
$stmt->execute([$RUT_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || !password_verify($password, $usuario['password'])) {
    die("Contraseña incorrecta.");
}

// Actualizar datos
$update = $pdo->prepare("UPDATE usuarios SET nombre = ?, email = ?, tipo_usuario = ? WHERE RUT_usuario = ?");
$update->execute([$nombre, $email, $tipo_usuario, $RUT_usuario]);

echo "Perfil actualizado correctamente. <a href='perfil.php'>Volver</a>";
?>