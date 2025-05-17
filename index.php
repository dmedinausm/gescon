<?php
$page = $_GET['page'] ?? 'login'; // Página por defecto: login
//require_once __DIR__ . '/config/db.php';
switch($page) {
  case 'login':
    include 'src/pages/login.php';
    break;
  case 'register':
    include 'src/pages/register.php';
    break;
  case 'main':
    include 'src/pages/main.php';
    break;
  case 'view_article':
    include 'src/pages/view_article.php';
    break;      
  case 'post_article':
    include 'src/pages/post_article.php';
    break;  
  case 'edit_article':
    include 'src/pages/edit_article.php';
    break;    
  case 'gestion_revisores':
    include 'src/pages/gestion_revisores.php';
    break;
  case 'asignar_articulos':
    include 'src/pages/asignar_articulos.php';
    break;
  case 'acceso_denegado':
    include 'src/pages/acceso_denegado.php';
    break;
  case 'perfil':
    include 'src/pages/perfil.php';
    break;
  default:
    echo "<h1>404 - Página no encontrada</h1>";
    break;
}
?>