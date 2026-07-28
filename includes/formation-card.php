<div class="col-6 col-md-4 col-lg-3">
  <div class="card h-100 shadow-sm school-card <?= $f['ecole_plan'] === 'premium' ? 'formation-premium' : '' ?>">
    <a href="formation?id=<?= $f['id'] ?>">
      <img src="<?= $f['image'] ? UPLOAD_URL_FORMATIONS . e($f['image']) : 'https://placehold.co/300x160?text=' . urlencode($f['nom']) ?>"
           class="card-img-top" style="height:130px;object-fit:cover;" alt="<?= e($f['nom']) ?>">
    </a>
    <div class="card-body p-3">
      <?php if ($f['ecole_plan'] === 'premium'): ?>
        <span class="badge bg-warning text-dark mb-1 small"><i class="bi bi-patch-check-fill"></i> Vérifiée</span>
      <?php endif; ?>
      <h6 class="card-title fw-bold mb-1 small"><?= e($f['nom']) ?></h6>
      <?php if (!empty($f['ecole_nom'])): ?>
        <p class="text-muted small mb-2"><i class="bi bi-building"></i> <?= e($f['ecole_nom']) ?></p>
      <?php endif; ?>
      <div class="d-flex flex-wrap gap-1">
        <?php if ($f['niveau']): ?><span class="badge bg-primary-subtle text-primary small"><?= e($f['niveau']) ?></span><?php endif; ?>
        <?php if ($f['duree']): ?><span class="badge bg-light text-dark border small"><?= e($f['duree']) ?></span><?php endif; ?>
      </div>
    </div>
    <div class="card-footer bg-white border-0 p-2">
      <a href="formation?id=<?= $f['id'] ?>" class="btn btn-outline-primary btn-sm w-100">Voir la formation</a>
    </div>
  </div>
</div>
