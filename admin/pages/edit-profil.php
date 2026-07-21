<?php
$pageTitle = 'Profil de l\'école';
require_once '../includes/admin-header.php';

?>

<h2 class="fw-bold mb-4">Profil de l'école</h2>

<form method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="fw-bold mb-3">Images</h5>
      <div class="row g-4">
        <div class="col-md-4">
          <label class="form-label d-block">Logo</label>
          <img id="logo-preview" class="upload-preview mb-2 <?= $ecole['logo'] ? '' : 'd-none' ?>"
               src="<?= $ecole['logo'] ? '../' . UPLOAD_URL_LOGOS . e($ecole['logo']) : '' ?>">
          <input type="file" name="logo" class="form-control" accept="image/*" data-preview="#logo-preview">
        </div>
        <div class="col-md-8">
          <label class="form-label d-block">Photo de couverture</label>
          <img id="cover-preview" class="upload-preview mb-2 <?= $ecole['cover_image'] ? '' : 'd-none' ?>"
               src="<?= $ecole['cover_image'] ? '../' . UPLOAD_URL_COVERS . e($ecole['cover_image']) : '' ?>">
          <input type="file" name="cover_image" class="form-control" accept="image/*" data-preview="#cover-preview">
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="fw-bold mb-3">Informations générales</h5>
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label">Nom de l'école *</label>
          <input type="text" name="nom" class="form-control" value="<?= e($ecole['nom']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Domaine</label>
          <select name="domaine" class="form-select">
            <?php foreach (['Formation professionnelle','Université / Enseignement supérieur','Informatique / Numérique','Santé','Commerce / Gestion','BTP / Industrie','Hôtellerie / Tourisme','Langues'] as $d): ?>
              <option <?= $ecole['domaine'] === $d ? 'selected' : '' ?>><?= e($d) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" rows="5" class="form-control" placeholder="Présentez votre établissement, son histoire, ses points forts..."><?= e($ecole['description']) ?></textarea>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="fw-bold mb-3">Localisation & Contact</h5>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Ville</label>
          <input type="text" name="ville" class="form-control" value="<?= e($ecole['ville']) ?>" placeholder="Douala, Yaoundé...">
        </div>
        <div class="col-md-8">
          <label class="form-label">Adresse complète</label>
          <input type="text" name="adresse" class="form-control" value="<?= e($ecole['adresse']) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Téléphone (WhatsApp)</label>
          <input type="text" name="telephone" class="form-control" value="<?= e($ecole['telephone']) ?>" placeholder="+237 6XX XXX XXX">
        </div>
        <div class="col-md-4">
          <label class="form-label">Email de contact</label>
          <input type="email" name="email" class="form-control" value="<?= e($ecole['email']) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Site web</label>
          <input type="url" name="site_web" class="form-control" value="<?= e($ecole['site_web']) ?>" placeholder="https://...">
        </div>
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary px-4">Enregistrer les modifications</button>
</form>

<?php require_once '../includes/admin-footer.php'; ?>
