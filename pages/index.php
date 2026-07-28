<?php
require_once 'includes/header.php';
?>

<!-- HERO -->
<section class="hero-section text-white text-center py-5">
  <div class="container py-4">
    <h1 class="display-5 fw-bold mb-3">Trouvez votre école de formation au Cameroun</h1>
    <p class="lead mb-4">Universités, instituts techniques, centres professionnels — comparez et contactez-les en un clic.</p>
    <form action="recherche" method="get" class="row justify-content-center g-2">
      <div class="col-md-5">
        <input type="text" name="q" class="form-control form-control-lg" placeholder="Nom, domaine (ex: informatique, santé, BTP...)">
      </div>
      <div class="col-md-3">
        <select name="ville" class="form-select form-select-lg">
          <option value="">Toutes les villes</option>
          <?php foreach ($villes as $v): ?>
            <option value="<?= e($v) ?>"><?= e($v) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-warning btn-lg w-100 fw-semibold" type="submit"><i class="bi bi-search"></i> Chercher</button>
      </div>
    </form>
  </div>
</section>

<div class="container py-5">

  <div class="row text-center mb-5">
    <div class="col-md-4">
      <h2 class="fw-bold text-primary"><?= (int)$totalEcoles ?>+</h2>
      <p class="text-muted">Écoles référencées</p>
    </div>
    <div class="col-md-4">
      <h2 class="fw-bold text-primary"><?= count($villes) ?>+</h2>
      <p class="text-muted">Villes couvertes</p>
    </div>
    <div class="col-md-4">
      <h2 class="fw-bold text-primary">100%</h2>
      <p class="text-muted">Gratuit pour les étudiants</p>
    </div>
  </div>

  <h2 class="fw-bold mb-4">Écoles à la une</h2>
  <div class="row g-4">
    <?php if (empty($ecoles)): ?>
      <p class="text-muted">Aucune école n'est encore publiée. <a href="register">Soyez la première à inscrire votre établissement</a>.</p>
    <?php endif; ?>
    <?php foreach ($ecoles as $ecole): ?>
      <?php include 'includes/school-card.php'; ?>
    <?php endforeach; ?>
  </div>

  <div class="text-center mt-5 mb-5">
    <a href="recherche" class="btn btn-primary btn-lg">Voir toutes les écoles <i class="bi bi-arrow-right"></i></a>
  </div>

  <!-- Formations à la une (priorité aux écoles premium) -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Formations à la une</h2>
    <a href="formations" class="btn btn-outline-primary btn-sm">Voir toutes les formations <i class="bi bi-arrow-right"></i></a>
  </div>
  <div class="row g-4">
    <?php if (empty($formationsAlaUne)): ?>
      <p class="text-muted">Aucune formation publiée pour le moment.</p>
    <?php endif; ?>
    <?php foreach ($formationsAlaUne as $f): ?>
      <?php include 'includes/formation-card.php'; ?>
    <?php endforeach; ?>
  </div>

  <div class="card bg-primary text-white mt-5 border-0">
    <div class="card-body text-center py-5">
      <h3 class="fw-bold">Vous dirigez un établissement de formation ?</h3>
      <p class="mb-4">Créez gratuitement votre vitrine et touchez des milliers d'étudiants camerounais.</p>
      <a href="register" class="btn btn-warning btn-lg fw-semibold">Inscrire mon école</a>
    </div>
  </div>

</div>

<?php require_once 'includes/footer.php'; ?>

