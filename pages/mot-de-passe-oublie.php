<?php
$pageTitle = 'Mot de passe oublié';
require_once 'includes/header.php';
// require_once __DIR__ . '/includes/mailer.php';

?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h2 class="fw-bold text-center mb-2"><i class="bi bi-key text-primary"></i> Mot de passe oublié</h2>
          <p class="text-muted text-center mb-4">Saisissez votre email : nous vous enverrons un code de réinitialisation.</p>

          <?php if ($errors): ?>
            <div class="alert alert-danger"><?php foreach ($errors as $err): ?><?= e($err) ?><?php endforeach; ?></div>
          <?php endif; ?>

          <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="mb-3">
              <label class="form-label">Adresse email</label>
              <input type="email" name="email" class="form-control" required autofocus value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary w-100">Recevoir un code</button>
          </form>

          <p class="text-center mt-3 mb-0 small text-muted">
            <a href="login">Retour à la connexion</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
