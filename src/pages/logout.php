<?php
session_start();

if (isset($_GET['page']) && $_GET['page'] == 'logout') {
    $_SESSION = array();
    session_destroy();
    header("Location: ?page=login");
    exit;
}
?>
