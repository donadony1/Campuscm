<?php
// require_once __DIR__ . '/config.php';
// require_once __DIR__ . '/functions.php';
// require_once __DIR__ . '/auth.php';
// $flash = get_flash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) . ' - ' . APP_NAME : APP_NAME . ' | Écoles de formation au Cameroun' ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="home"><i class="bi bi-mortarboard-fill"></i> <?= APP_NAME ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <li class="nav-item"><a class="nav-link" href="home">Accueil</a></li>
        <li class="nav-item"><a class="nav-link" href="recherche">Trouver une école</a></li>
        <?php if (is_logged_in()): ?>
          <?php if (current_user()['role'] === 'super_admin'): ?>
            <li class="nav-item"><a class="nav-link" href="superadmin/dashboard.php">Back-office</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="admin/dashboard">Mon dashboard</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="logout">Déconnexion</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login">Connexion</a></li>
          <li class="nav-item"><a class="btn btn-warning btn-sm fw-semibold ms-lg-2" href="register">Inscrire mon école</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<?php if ($flash): ?>
<div class="container mt-3">
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
    <?= e($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
</div>
<?php endif; ?>
