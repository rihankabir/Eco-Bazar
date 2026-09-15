<?php
require_once __DIR__ . '/functions.php';

if(!isset($_SESSION['user_id'])){
    
      header('Location: /Ecomart/auth/login.php');

    exit;
}


 ?>