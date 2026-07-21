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
      <div class="col-md-4">
        <div class="card h-100 shadow-sm school-card">
          <img src="<?= $ecole['cover_image'] ? UPLOAD_URL_COVERS . e($ecole['cover_image']) : '' ?>"
               class="card-img-top" style="height:160px;object-fit:cover;" alt="<?= e($ecole['nom']) ?>"
               onerror="this.src='https://placehold.co/400x160?text=' + encodeURIComponent('<?= e($ecole['nom']) ?>')">
          <div class="card-body">
            <h5 class="card-title fw-bold">
              <?= e($ecole['nom']) ?>
              <?php if ($ecole['plan'] === 'premium'): ?><span class="badge bg-warning text-dark"><i class="bi bi-patch-check-fill"></i> Vérifié</span><?php endif; ?>
            </h5>
            <p class="text-muted small mb-1"><i class="bi bi-geo-alt"></i> <?= e($ecole['ville'] ?: 'Cameroun') ?></p>
            <span class="badge bg-primary-subtle text-primary mb-2"><?= e($ecole['domaine']) ?></span>
            <p class="card-text small"><?= e(mb_strimwidth($ecole['description'] ?? '', 0, 100, '...')) ?></p>
          </div>
          <div class="card-footer bg-white border-0">
            <a href="ecole?slug=<?= e($ecole['slug']) ?>" class="btn btn-outline-primary btn-sm w-100">Voir la vitrine</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
