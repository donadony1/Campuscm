<?php
$pageTitle = 'Vérification de votre email';
require_once 'includes/header.php';


?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm">
        <div class="card-body p-4 text-center">
          <i class="bi bi-envelope-check display-4 text-primary"></i>
          <h2 class="fw-bold mt-3">Vérifiez votre email</h2>
          <p class="text-muted">Un code à 6 chiffres a été envoyé à<br><strong><?= e($email) ?></strong></p>

          <?php if ($success): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
          <?php endif; ?>
          <?php if ($errors): ?>
            <div class="alert alert-danger"><ul class="mb-0 text-start"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
          <?php endif; ?>

          <form method="post" class="mt-3">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="email" value="<?= e($email) ?>">
            <input type="text" name="code" class="form-control form-control-lg text-center mb-3"
                   style="letter-spacing: 8px; font-weight:bold;" maxlength="6" pattern="\d{6}"
                   placeholder="------" autofocus required>
            <button type="submit" name="verifier" class="btn btn-primary w-100">Vérifier mon compte</button>
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