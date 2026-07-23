<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_role('super_admin');
$flash = get_flash();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) : 'Back-office' ?> - <?= APP_NAME ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Ouvrir le menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <span class="navbar-brand fw-bold"><i class="bi bi-shield-lock-fill"></i> <?= APP_NAME ?> — Back-office</span>
    <div class="d-flex align-items-center gap-3">
      <span class="text-white small d-none d-sm-inline"><?= e(current_user()['nom']) ?></span>
      <a href="../logout" class="btn btn-sm btn-outline-light">Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-white border-end sidebar collapse py-4">
      <ul class="nav flex-column gap-1">
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'dashboard.php' ? 'active fw-semibold' : 'text-dark' ?>" href="dashboard.php">
            <i class="bi bi-speedometer2"></i> Vue d'ensemble
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'ecoles.php' ? 'active fw-semibold' : 'text-dark' ?>" href="ecoles.php">
            <i class="bi bi-building"></i> Écoles
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage === 'paiements.php' ? 'active fw-semibold' : 'text-dark' ?>" href="paiements.php">
            <i class="bi bi-cash-coin"></i> Paiements
          </a>
        </li>
      </ul>
    </nav>

    <main class="col-md-9 col-lg-10 ms-sm-auto py-4">
      <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
          <?= e($flash['message']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
