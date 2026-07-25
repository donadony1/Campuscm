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

// Authentification vérifiée AVANT tout traitement (évite d'exécuter du code
// de traitement avec un utilisateur non connecté, qui produirait des
// warnings PHP en tentant d'accéder à current_user()['ecole_id'] sur null).
require_login();
if (current_user()['role'] !== 'admin_ecole') {
    http_response_code(403);
    die('Accès réservé aux administrateurs d\'école.');
}

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

    case 'actions':
        // Endpoint AJAX (JSON) - pas de vue associée
        include 'processing/pro_actions.php';
        break;
    default:
        // Page not found
     
        break;
}