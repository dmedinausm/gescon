<?php
session_start();
require_once 'config\db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$rut = $_SESSION['usuario'];
$tipo_usuario = $_SESSION['tipo_usuario'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nombre = $_POST['nombre'];
    $email= $_POST['email'];
    $nueva_contraseña = $_POST['nueva_contraseña'];
    $confirmar_contraseña = $_POST['confirmar_contraseña'];

    
    if (!empty($nueva_contraseña) && $nueva_contraseña !== $confirmar_contraseña) {
        $error = "Las contraseñas no coinciden.";
    }
    else {
        // Preparar consulta de actualización
        if (!empty($nueva_contraseña)) {
            $password_hash = password_hash($nueva_contraseña, PASSWORD_DEFAULT);
            $sql = "UPDATE usuario SET nombre=?, email=?, password_hash=? WHERE RUT_usuario=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $nombre, $email, $password_hash, $rut); //ssss indica que tenemos 4 parametros de tipo string
        } else {
            $sql = "UPDATE usuario SET nombre=?, email=? WHERE RUT_usuario=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $nombre, $email, $rut);
        }

        $_SESSION['nombre'] = $nombre;

        //Si es revisor, 
        if($tipo_usuario === 'R') {
            //limpar anteriores
            $pdo->prepare("DELETE FROM revisor_topico WHERE RUT_revisor = ?")->execute([$rut]);
        
            //insertar nuevos
            if(!empty($_POST['topicos'])) {
                $stmt = $pdo->prepare("INSERT INTO revisor_topico (RUT_revisor, ID_topico) VALUES (?,?)");
                foreach ($_POST['topicos'] as $id_topico){
                    $stmt->execute([$rut, $id_topico]);
                }
            }
        }

        $success = "Datos actualizados correctamente (topico y +)";
    

        // if($stmt->execute()){
        //     $success = "Datos actualizados correctamente.";
        //     $_SESSION['nombre'] = $nombre;
        // }else {
        //     $error = "Error al actualizar los datos.";
        // }
    }
    //Obtener los datos actuales
}

//obtener los datos del usuario
$stmt = $conn->prepare("SELECT nombre, email FROM usuario WHERE RUT_usuario = ?");
$stmt->bind_param("s",$rut);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

//si es revisor, obtener topicos
$topicos = [];
$topicos_asignados = [];
if ($tipo_usuario === 'R') {
    $topicos = $pdo->query("SELECT ID_topico, nombre_topico FROM topico")->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT ID_topico FROM revisor_topico WHERE RUT_revisor = ?");
    $stmt->execute([$rut]);
    $topicos_asignados = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Perfil de Usuario</title>
</head>
<body>
     <h1>GESCON</h1>
        <a href="?page=main">Ir al Menu</a>
    <h2>Perfil de <?php echo htmlspecialchars($_SESSION['nombre']); ?></h2>

    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required><br>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required><br>

        <?php if($tipo_usuario === 'R'): ?>
            <fieldset>
                <legend>Especialidades (Tópicos)</legend>
                <?php foreach ($topicos as $topico): ?>
                    <label>
                        <input type="checkbox" name="topicos[]" value="<?= $topico['ID_topico'] ?>"
                            <?= in_array($topico['ID_topico'], $topicos_asignados) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($topico['nombre_topico']) ?>
                    </label><br>
                <?php endforeach; ?>
            </fieldset>
        <?php endif; ?>
        
        <label>Nueva contraseña:</label>
        <input type="password" name="nueva_contraseña"><br>

        <label>Confirmar contraseña:</label>
        <input type="password" name="confirmar_contraseña"><br>

        <input type="submit" value="Actualizar perfil">
    </form>
</body>
</html>












