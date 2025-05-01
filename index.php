<?php
$page = $_GET['page'] ?? 'login'; // Página por defecto: login
// require_once __DIR__ . '/config/db.php';
switch($page) {
  case 'login':
    include 'src/pages/login.php';
    break;
  case 'register':
    include 'src/pages/register.php';
    break;
    case 'register.php':
      include 'src/pages/register.php';
      break;
  case 'main':
    include 'src/pages/main.php';
    break;

  default:
    echo "<h1>404 - Página no encontrada</h1>";
    break;
}
