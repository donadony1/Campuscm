<?php
// require_once __DIR__ . '/config.php';
// require_once __DIR__ . '/functions.php';
// require_once __DIR__ . '/auth.php';
// require_login();

if (current_user()['role'] !== 'admin_ecole') {
    http_response_code(403);
    die('Accès réservé aux administrateurs d\'école.');
}
$flash = get_flash();
$currentPage = $route ;
// $currentPage = basename($_SERVER['PHP_SELF']);

$__stmt = getPDO()->prepare('SELECT slug, premium_jusqu_au FROM ecoles WHERE id = ?');
$__stmt->execute([current_user()['ecole_id']]);
$__ecoleRow = $__stmt->fetch();
$__ecoleSlug = $__ecoleRow['slug'] ?? '';
$__premiumBadge = is_premium_active($__ecoleRow['premium_jusqu_au'] ?? null);


?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) : 'Dashboard' ?> - <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-primary shadow-sm">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold"><i class="bi bi-mortarboard-fill"></i> <?= APP_NAME ?> — Espace école</span>
    <div class="d-flex align-items-center gap-3">
      <span class="text-white small d-none d-sm-inline"><?= e(current_user()['nom']) ?></span>
      <a href="../logout" class="btn btn-sm btn-outline-light">Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
    <nav class="col-md-3 col-lg-2 d-md-block bg-white border-end sidebar py-4" style="min-height: calc(100vh - 56px);">
      <ul class="nav flex-column gap-1">
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'dashboard' ? 'active fw-semibold' : 'text-dark' ?>" href="dashboard">
            <i class="bi bi-speedometer2"></i> Tableau de bord
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'edit-profil' ? 'active fw-semibold' : 'text-dark' ?>" href="edit-profil">
            <i class="bi bi-building"></i> Profil de l'école
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'filieres' ? 'active fw-semibold' : 'text-dark' ?>" href="filieres">
            <i class="bi bi-journal-bookmark"></i> Filières / Formations
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'photos' ? 'active fw-semibold' : 'text-dark' ?>" href="photos">
            <i class="bi bi-images"></i> Galerie photos
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'abonnement' ? 'active fw-semibold' : 'text-dark' ?>" href="abonnement">
            <i class="bi bi-star"></i> Abonnement <?php if (($__premiumBadge ?? false)): ?><span class="badge bg-warning text-dark ms-1">Premium</span><?php endif; ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../ecole?slug=<?= e($__ecoleSlug) ?>" target="_blank">
            <i class="bi bi-eye"></i> Voir ma vitrine
          </a>
        </li>
      </ul>
    </nav>

    <main class="col-md-9 col-lg-10 py-4">
      <?php if ($flash):?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
          <?= e($flash['message']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
