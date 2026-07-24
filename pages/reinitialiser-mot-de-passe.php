
<?php
$pageTitle = 'Réinitialiser mon mot de passe';
require_once 'includes/header.php';
// require_once __DIR__ . '/includes/mailer.php';

?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body p-4 text-center">
          <i class="bi bi-shield-lock display-4 text-primary"></i>
          <h2 class="fw-bold mt-3">Réinitialiser mon mot de passe</h2>
          <p class="text-muted">Si un compte existe pour<br><strong><?= e($email) ?></strong>, un code à 6 chiffres lui a été envoyé.</p>

          <?php if ($success): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
          <?php endif; ?>
          <?php if ($errors): ?>
            <div class="alert alert-danger"><ul class="mb-0 text-start"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
          <?php endif; ?>

          <form method="post" class="mt-3 text-start">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="email" value="<?= e($email) ?>">

            <label class="form-label">Code reçu par email</label>
            <input type="text" name="code" class="form-control form-control-lg text-center mb-3"
                   style="letter-spacing: 8px; font-weight:bold;" maxlength="6" pattern="\d{6}" inputmode="numeric"
                   placeholder="------" autofocus required>

            <label class="form-label">Nouveau mot de passe</label>
            <input type="password" name="password" class="form-control mb-3" required>

            <label class="form-label">Confirmer le nouveau mot de passe</label>
            <input type="password" name="password_confirm" class="form-control mb-3" required>

            <button type="submit" name="reinitialiser" class="btn btn-primary w-100">Réinitialiser mon mot de passe</button>
          </form>

          <form method="post" class="mt-3">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="email" value="<?= e($email) ?>">
            <button type="submit" name="renvoyer" class="btn btn-link btn-sm">Je n'ai pas reçu de code, renvoyer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
