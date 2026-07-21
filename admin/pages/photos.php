<?php
$pageTitle = 'Galerie photos';
require_once '../includes/admin-header.php';
?>

<h2 class="fw-bold mb-4">Galerie photos</h2>

<div class="card shadow-sm mb-4">
  <div class="card-body">
    <form method="post" enctype="multipart/form-data" class="row g-2 align-items-end">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <div class="col-md-8">
        <label class="form-label">Ajouter une photo (jpg, png, webp — max 3 Mo)</label>
        <input type="file" name="photo" class="form-control" accept="image/*" required>
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-upload"></i> Envoyer</button>
      </div>
    </form>
  </div>
</div>

<div class="row g-3" id="gallery-container">
  <?php foreach ($photos as $p): ?>
    <div class="col-6 col-md-3 photo-item">
      <div class="card shadow-sm">
        <img src="../<?= UPLOAD_URL_PHOTOS . e($p['chemin']) ?>" class="card-img-top" style="height:130px;object-fit:cover;">
        <div class="card-body p-2 text-center">
          <button class="btn btn-sm btn-outline-danger delete-photo-btn" data-id="<?= $p['id'] ?>">
            <i class="bi bi-trash"></i> Supprimer
          </button>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($photos)): ?>
    <p class="text-muted">Aucune photo pour le moment.</p>
  <?php endif; ?>
</div>

<input type="hidden" id="csrf_token_val" value="<?= e(csrf_token()) ?>">

<?php require_once '../includes/admin-footer.php'; ?>
