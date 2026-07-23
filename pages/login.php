<?php
$pageTitle = 'Connexion';
require_once 'includes/header.php';

?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h2 class="fw-bold text-center mb-4"><i class="bi bi-mortarboard-fill text-primary"></i> Connexion</h2>

          <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
          <?php endif; ?>

          <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
              <label class="form-label">Adresse email</label>
              <input type="email" name="email" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label">Mot de passe</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
          </form>

          <p class="text-center mt-3 mb-0 small text-muted">
            Pas encore de compte ? <a href="register.php">Inscrire mon école</a>
          </p>
          <div class="alert alert-secondary small mt-3 mb-0">
            <?php /* <strong>Démo super-admin :</strong> admin@campuscm.cm / admin123 */ ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
