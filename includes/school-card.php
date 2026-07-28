<div class="col-md-4">
  <div class="card h-100 shadow-sm school-card">
    <a href="ecole?slug=<?= e($ecole['slug']) ?>">
      <img src="<?= $ecole['cover_image'] ? UPLOAD_URL_COVERS . e($ecole['cover_image']) : 'assets/img/placeholder-cover.jpg' ?>"
           class="card-img-top" style="height:160px;object-fit:cover;" alt="<?= e($ecole['nom']) ?>"
           onerror="this.src='https://placehold.co/400x160?text=' + encodeURIComponent('<?= e($ecole['nom']) ?>')">
    </a>
    <div class="card-body">
      <h5 class="card-title fw-bold">
        <?= e($ecole['nom']) ?>
        <?php if ($ecole['plan'] === 'premium'): ?><span class="badge bg-warning text-dark"><i class="bi bi-patch-check-fill"></i> Vérifié</span><?php endif; ?>
      </h5>
      <p class="text-muted small mb-1"><i class="bi bi-geo-alt"></i> <?= e($ecole['ville'] ?: 'Cameroun') ?></p>
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="badge bg-primary-subtle text-primary"><?= e($ecole['domaine']) ?></span>
        <span class="text-muted small"><i class="bi bi-eye"></i> <?= (int)($ecole['vues'] ?? 0) ?> vue<?= (int)($ecole['vues'] ?? 0) > 1 ? 's' : '' ?></span>
      </div>
      <p class="card-text small text-truncate-3"><?= e(mb_strimwidth($ecole['description'] ?? '', 0, 120, '...')) ?></p>
    </div>
    <div class="card-footer bg-white border-0">
      <a href="ecole?slug=<?= e($ecole['slug']) ?>" class="btn btn-outline-primary btn-sm w-100">Voir la vitrine</a>
    </div>
  </div>
</div>
