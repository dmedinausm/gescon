<h1>Página de Login</h1>
<p>Bienvenido, por favor inicia sesión.</p>
<a href="main">Ir a Main</a>


<form action="login.php" method="post">
  <input type="email" name="email" placeholder="Email" required>
  
  <input type="password" name="password" placeholder="Contraseña" required>
  <!-- <input type="select" name="user_type" option="1" required> -->
  <select name="select">
    <option value="Autor" selected>Autor</option>
    <option value="Revisor">Revisor</option>
    </select>
  <button type="submit">Ingresar</button>
</form>
<p>¿No tienes cuenta? <a href="register">Regístrate aquí</a></p>

<!-- <?php

session_start();

// $pdo = newPDO("mysql:host=localhost; dbname=gescon", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// obtener datos del formulario

$email = $_POST['email'];
$contraseña = $_POST['password'];

// buscar al usuario por email

// $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = : email")


?> -->