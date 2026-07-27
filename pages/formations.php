<?php
$pageTitle = 'Toutes les formations';
require_once 'includes/header.php';
?>

<div class="container py-5">
  <h1 class="fw-bold mb-4"><i class="bi bi-journal-bookmark text-primary"></i> Formations</h1>

  <form method="get" class="row g-2 mb-5 bg-white p-3 rounded shadow-sm">
    <div class="col-md-5">
      <input type="text" name="q" class="form-control" placeholder="Nom de la formation, mot-clé..." value="<?= e($q) ?>">
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

  <p class="text-muted mb-4"><?= count($formations) ?> formation(s)</p>

  <div class="row g-4">
    <?php if (empty($formations)): ?>
      <p class="text-muted">Aucune formation ne correspond à votre recherche.</p>
    <?php endif; ?>
    <?php foreach ($formations as $f): ?>
      <div class="col-md-4 col-lg-3">
        <div class="card h-100 shadow-sm school-card <?= $f['ecole_plan'] === 'premium' ? 'formation-premium' : '' ?>">
          <a href="formation?id=<?= $f['id'] ?>">
            <img src="<?= $f['image'] ? UPLOAD_URL_FORMATIONS . e($f['image']) : 'https://placehold.co/400x220?text=' . urlencode($f['nom']) ?>"
                 class="card-img-top" style="height:150px;object-fit:cover;" alt="<?= e($f['nom']) ?>">
          </a>
          <div class="card-body">
            <?php if ($f['ecole_plan'] === 'premium'): ?>
              <span class="badge bg-warning text-dark mb-2"><i class="bi bi-patch-check-fill"></i> École vérifiée</span>
            <?php endif; ?>
            <h6 class="card-title fw-bold mb-1"><?= e($f['nom']) ?></h6>
            <p class="text-muted small mb-2">
              <i class="bi bi-building"></i> <a href="ecole?slug=<?= e($f['ecole_slug']) ?>" class="text-decoration-none text-muted"><?= e($f['ecole_nom']) ?></a>
            </p>
            <p class="small mb-0">
              <?php if ($f['niveau']): ?><span class="badge bg-primary-subtle text-primary"><?= e($f['niveau']) ?></span><?php endif; ?>
              <?php if ($f['duree']): ?><span class="badge bg-light text-dark border"><?= e($f['duree']) ?></span><?php endif; ?>
            </p>
          </div>
          <div class="card-footer bg-white border-0">
            <a href="formation?id=<?= $f['id'] ?>" class="btn btn-outline-primary btn-sm w-100">Voir la formation</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
