<?php
$pageTitle = 'Rechercher une école';
require_once 'includes/header.php';
?>

<div class="container py-5">
  <h1 class="fw-bold mb-4">Rechercher une école</h1>

  <form method="get" class="row g-2 mb-5 bg-white p-3 rounded shadow-sm">
    <div class="col-md-5">
      <input type="text" name="q" class="form-control" placeholder="Nom, mot-clé..." value="<?= e($q) ?>">
    </div>
    <div class="col-md-3">
      <select name="ville" class="form-select">
        <option value="">Toutes les villes</option>
        <?php foreach ($villes as $v): ?>
          <option value="<?= e($v) ?>" <?= $ville === $v ? 'selected' : '' ?>><?= e($v) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <select name="domaine" class="form-select">
        <option value="">Tous les domaines</option>
        <?php foreach ($domaines as $d): ?>
          <option value="<?= e($d) ?>" <?= $domaine === $d ? 'selected' : '' ?>><?= e($d) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-1">
      <button class="btn btn-primary w-100" type="submit"><i class="bi bi-search"></i></button>
    </div>
  </form>

  <p class="text-muted mb-4"><?= count($resultats) ?> résultat(s)</p>

  <div class="row g-4">
    <?php if (empty($resultats)): ?>
      <p class="text-muted">Aucune école ne correspond à votre recherche.</p>
    <?php endif; ?>
    <?php foreach ($resultats as $ecole): ?>
      <?php include 'includes/school-card.php'; ?>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
