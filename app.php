<?php 

if (isset($_GET['url'])) {
    $url = explode('/', trim($_GET['url'], '/'));
}
// On inclut les fichiers de configuration et fonctions
require_once  'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
$flash = get_flash();

$pdo = getPDO();

$route = $url[0] ?? '';
$route = $route ?: 'home';

switch($route) {
    case 'home':

        // On rétrograde d'abord les écoles dont le premium a expiré (portable MySQL/SQLite)
        downgrade_expired_premiums($pdo);

        // Écoles mises en avant (Premium en priorité, puis les plus récentes)
        $stmt = $pdo->query("SELECT * FROM ecoles WHERE statut = 'valide' ORDER BY (plan = 'premium') DESC, date_creation DESC LIMIT 6");
        $ecoles = $stmt->fetchAll();

        $totalEcoles = $pdo->query("SELECT COUNT(*) AS n FROM ecoles WHERE statut = 'valide'")->fetch()['n'];
        $villesStmt = $pdo->query("SELECT DISTINCT ville FROM ecoles WHERE statut = 'valide' AND ville IS NOT NULL AND ville != ''");
        $villes = $villesStmt->fetchAll(PDO::FETCH_COLUMN);
        // On inclut la page d'accueil
        include 'pages/index.php';

    break;

    case 'recherche':
        // On inclut le fichier de traitement de la recherche
        include 'processing/req_recherche.php';
        // On inclut la page de recherche
        include 'pages/recherche.php';
        break;

    case 'ecole':
        // On inclut le fichier de traitement de la vitrine d'école
        include 'processing/req_ecole.php';
        include 'pages/ecole.php';
        break;

    case 'register':
        // On inclut le fichier de traitement de l'inscription d'école
        include 'processing/req_register.php';
        include 'pages/register.php';
        break;

    case 'verify-email':
        include 'processing/req_verify_email.php';
        include 'pages/verify-email.php';
        break;

    case 'login':
        include ('processing/req_login.php');
        include ('pages/login.php');
        break;

    case 'logout':
        include ('processing/req_logout.php');
        include ('pages/logout.php');
        break;
    default:
        // Page not found
     
        break;
}