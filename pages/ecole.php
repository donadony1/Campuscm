<?php

require_once  'includes/header.php';


?>

<!-- Bannière de couverture -->
<div class="cover-banner" style="background-image:url('<?= $ecole['cover_image'] ? UPLOAD_URL_COVERS . e($ecole['cover_image']) : 'https://placehold.co/1200x300?text=' . urlencode($ecole['nom']) ?>');">
  <div class="cover-overlay"></div>
</div>

<div class="container">
  <div class="row">
    <div class="col-12">
      <div class="d-flex align-items-end school-header-card bg-white rounded shadow-sm p-4">
        <img src="<?= $ecole['logo'] ? UPLOAD_URL_LOGOS . e($ecole['logo']) : 'https://placehold.co/100x100?text=Logo' ?>"
             class="rounded border bg-white" style="width:100px;height:100px;object-fit:cover;" alt="Logo <?= e($ecole['nom']) ?>">
        <div class="ms-4">
          <h1 class="fw-bold mb-1"><?= e($ecole['nom']) ?></h1>
          <span class="badge bg-primary-subtle text-primary"><?= e($ecole['domaine']) ?></span>
          <?php if ($ecole['plan'] === 'premium'): ?>
            <span class="badge bg-warning text-dark"><i class="bi bi-patch-check-fill"></i> Vérifié</span>
          <?php endif; ?>
          <?php if (count($avis)): ?>
            <span class="ms-2"><?= render_stars($noteMoyenne) ?> <small class="text-muted">(<?= count($avis) ?> avis)</small></span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4 g-4">
    <div class="col-lg-8">

      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h4 class="fw-bold mb-3">À propos</h4>
          <p style="white-space: pre-line;"><?= e($ecole['description'] ?: 'Aucune description fournie pour le moment.') ?></p>
        </div>
      </div>

      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h4 class="fw-bold mb-3"><i class="bi bi-journal-bookmark"></i> Filières & Formations</h4>
          <?php if (empty($filieres)): ?>
            <p class="text-muted">Aucune filière renseignée pour le moment.</p>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead><tr><th>Filière</th><th>Niveau</th><th>Durée</th><th>Prix</th></tr></thead>
                <tbody>
                <?php foreach ($filieres as $f): ?>
                  <tr>
                    <td class="fw-semibold"><?= e($f['nom']) ?><?php if($f['description']): ?><br><small class="text-muted"><?= e($f['description']) ?></small><?php endif; ?></td>
                    <td><?= e($f['niveau']) ?></td>
                    <td><?= e($f['duree']) ?></td>
                    <td><?= e($f['prix']) ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <?php if (!empty($photos)): ?>
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h4 class="fw-bold mb-3"><i class="bi bi-images"></i> Galerie</h4>
          <div class="row g-2">
            <?php foreach ($photos as $p): ?>
              <div class="col-4 col-md-3">
                <a href="<?= UPLOAD_URL_PHOTOS . e($p['chemin']) ?>" target="_blank">
                  <img src="<?= UPLOAD_URL_PHOTOS . e($p['chemin']) ?>" class="img-fluid rounded gallery-thumb" style="height:100px;width:100%;object-fit:cover;">
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <div class="card shadow-sm mb-4" id="avis">
        <div class="card-body">
          <h4 class="fw-bold mb-3"><i class="bi bi-chat-dots"></i> Avis des visiteurs</h4>

          <?php foreach ($avis as $a): ?>
            <div class="border-bottom pb-2 mb-2">
              <strong><?= e($a['nom_visiteur']) ?></strong> <?= render_stars($a['note']) ?>
              <p class="mb-0 text-muted small"><?= e($a['commentaire']) ?></p>
            </div>
          <?php endforeach; ?>
          <?php if (empty($avis)): ?><p class="text-muted">Aucun avis pour le moment. Soyez le premier !</p><?php endif; ?>

          <form method="post" class="mt-3">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="row g-2">
              <div class="col-md-6">
                <input type="text" name="nom_visiteur" class="form-control" placeholder="Votre nom" required>
              </div>
              <div class="col-md-6">
                <select name="note" class="form-select">
                  <option value="5">★★★★★ Excellent</option>
                  <option value="4">★★★★ Très bien</option>
                  <option value="3">★★★ Correct</option>
                  <option value="2">★★ Moyen</option>
                  <option value="1">★ Décevant</option>
                </select>
              </div>
              <div class="col-12">
                <textarea name="commentaire" class="form-control" rows="2" placeholder="Votre commentaire..." required></textarea>
              </div>
              <div class="col-12">
                <button type="submit" name="ajouter_avis" class="btn btn-primary">Publier mon avis</button>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <h5 class="fw-bold mb-3">Contact</h5>
          <p class="mb-2"><i class="bi bi-geo-alt text-primary"></i> <?= e($ecole['adresse'] ?: $ecole['ville']) ?></p>
          <?php if ($ecole['telephone']): ?>
            <p class="mb-2"><i class="bi bi-telephone text-primary"></i> <a href="tel:<?= e($ecole['telephone']) ?>" class="text-decoration-none"><?= e($ecole['telephone']) ?></a></p>
          <?php endif; ?>
          <?php if ($ecole['email']): ?>
            <p class="mb-2"><i class="bi bi-envelope text-primary"></i> <a href="mailto:<?= e($ecole['email']) ?>" class="text-decoration-none"><?= e($ecole['email']) ?></a></p>
          <?php endif; ?>
          <?php if ($ecole['site_web']): ?>
            <p class="mb-2"><i class="bi bi-globe text-primary"></i> <a href="<?= e($ecole['site_web']) ?>" target="_blank" class="text-decoration-none">Site web</a></p>
          <?php endif; ?>
          <?php if ($ecole['telephone']): ?>
          <a href="https://wa.me/<?= e(preg_replace('/\D/', '', $ecole['telephone'])) ?>" target="_blank" class="btn btn-success w-100 mt-2">
            <i class="bi bi-whatsapp"></i> Contacter sur WhatsApp
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
