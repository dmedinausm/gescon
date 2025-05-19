<?php
$page = $_GET['page'] ?? 'login'; // Página por defecto: login
//require_once __DIR__ . '/config/db.php';
switch($page) {
  // case 'poblar':
  //   include 'BD/poblar.php';
  //   break;
  case 'login':
    include 'PHP/src/pages/_login/login.php';
    break;
  case 'register':
    include 'PHP/src/pages/_login/register.php';
    break;
  case 'main':
    include 'PHP/src/pages/main.php';
    break;
  //ARTICULOS
  case 'view_article':
    include 'PHP/src/pages/view_article.php';
    break;   
  case 'review_article':
    include 'PHP/src/pages/review_article.php';
    break;    
  case 'adv_search':
    include 'PHP/src/pages/adv_search.php';
    break;   
  case 'post_article':
    include 'PHP/src/pages/post_article.php';
    break;  
  case 'edit_article':
    include 'PHP/src/pages/edit_article.php';
    break;  
  case 'add_review':
    include 'PHP/src/pages/add_review.php';
    break;   
  case 'view_review':
    include 'PHP/src/pages/view_review.php';
    break;  
  case 'view_review_r':
    include 'PHP/src/pages/view_review_r.php';
    break;                        
  case 'review_list':
    include 'PHP/src/pages/review_list.php';
    break;     
  //REVISORES
  case 'gestion_revisores':
    include 'PHP/src/pages/gestion_revisores/gestion_revisores.php';
    break;
  case 'agregar_revisores':
    include 'PHP/src/pages/gestion_revisores/agregar_revisores.php';
    break;
  case 'procesar_agregar':
    include 'PHP/src/pages/gestion_revisores/procesar_agregar.php';
    break;
  case 'editar_revisores':
    include 'PHP/src/pages/gestion_revisores/editar_revisores.php';
    break;
  case 'eliminar_revisores':
    include 'PHP/src/pages/gestion_revisores/eliminar_revisores.php';
    break;
  //ASIGNAR REVISORES A ARTICULOS
  case 'asignar_artic_rev':
    include 'PHP/src/pages/asignar/asignar_artic_rev.php';
    break;
  case 'procesar_asignar_revisor':
    include 'PHP/src/pages/asignar/procesar_asignar_revisor.php';
    break;
  case 'quitar_revisor':
    include 'PHP/src/pages/asignar/quitar_revisor.php';
    break;
  case 'asignar_revisor':
    include 'PHP/src/pages/asignar/asignar_revisor.php';
    break;
  //ASIGNAR ARTICULOS A REVISORES
  case 'detalle_revisor':
    include 'PHP/src/pages/asignar/revisor/detalle_revisor.php';
    break;
  case 'asignar_articulo_revisor':
    include 'PHP/src/pages/asignar/revisor/asignar_articulo_revisor.php';
    break;
  case 'quitar_articulo_revisor':
    include 'PHP/src/pages/asignar/revisor/quitar_articulo_revisor.php';
    break;
  //OTROS
  case 'acceso_denegado':
    include 'PHP/src/pages/acceso_denegado.php';
    break;
  case 'perfil':
    include 'PHP/src/pages/perfil/perfil.php';
    break;
  case 'logout':
    include 'PHP/src/pages/logout.php';
  default:
    echo "<h1>404 - Página no encontrada</h1>";
    break;
}
?>