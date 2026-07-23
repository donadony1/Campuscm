<?php 

if (isset($_GET['url'])) {
    $url = explode('/', trim($_GET['url'], '/'));
}

// var_dump($url);
// exit;
// On inclut les fichiers de configuration et fonctions
require_once  '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

$pdo = getPDO();

$route = $url[0] ?? '';
$route = $route ?: 'dashboard';

switch($route) {
    case 'dashboard':
        include 'processing/pro_dasboard.php';
        include 'pages/dashboard.php';
        break;  

    case 'edit-profil':
        
        include 'processing/pro_edt.php';
        include 'pages/edit-profil.php';
        break;

    case 'filieres':
        include 'processing/pro_filieres.php';
        include 'pages/filieres.php';
        break; 
        
    case 'photos':
        include 'processing/pro_photos.php';
        include 'pages/photos.php';
        break;

    case 'abonnement':
        include 'processing/pro_abonnement.php';
        include 'pages/abonnement.php';
        break;
    default:
        // Page not found
     
        break;
}