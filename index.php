<?php
$page = $_GET['page'] ?? 'login'; // Página por defecto: login

switch($page) {
  case 'login':
    include 'src/pages/login.php';
    break;
  case 'main':
    include 'src/pages/main.php';
    break;
  default:
    echo "<h1>404 - Página no encontrada</h1>";
    break;
}
