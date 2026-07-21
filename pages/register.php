<?php
$pageTitle = 'Inscrire mon école';

require_once 'includes/header.php';

?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h2 class="fw-bold mb-1"><i class="bi bi-building-add text-primary"></i> Inscrire mon école</h2>
          <p class="text-muted mb-4">Créez votre compte gratuitement. Votre vitrine sera publiée après une brève validation.</p>

          <?php if ($errors): ?>
            <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
          <?php endif; ?>

          <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <h6 class="fw-bold text-primary mt-2">Établissement</h6>
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label">Nom de l'école *</label>
                <input type="text" name="nom_ecole" class="form-control" value="<?= e($_POST['nom_ecole'] ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Ville</label>
                <input type="text" name="ville" class="form-control" value="<?= e($_POST['ville'] ?? '') ?>" placeholder="Douala, Yaoundé...">
              </div>
              <div class="col-12">
                <label class="form-label">Domaine principal</label>
                <select name="domaine" class="form-select">
                  <option>Formation professionnelle</option>
                  <option>Collège / Lycée</option>
                  <option>Ecole Primere</option>
                  <option>Université / Enseignement supérieur</option>
                  <option>Informatique / Numérique</option>
                  <option>Santé</option>
                  <option>Commerce / Gestion</option>
                  <option>BTP / Industrie</option>
                  <option>Hôtellerie / Tourisme</option>
                  <option>Langues</option>
                </select>
              </div>
            </div>

            <h6 class="fw-bold text-primary mt-4">Votre compte administrateur</h6>
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label">Votre nom *</label>
                <input type="text" name="nom_admin" class="form-control" value="<?= e($_POST['nom_admin'] ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Mot de passe *</label>
                <input type="password" name="password" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Confirmer le mot de passe *</label>
                <input type="password" name="password_confirm" class="form-control" required>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Créer mon compte</button>
          </form>

          <p class="text-center mt-3 mb-0 small text-muted">
            Déjà inscrit ? <a href="login">Connectez-vous</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once  'includes/footer.php'; ?>
