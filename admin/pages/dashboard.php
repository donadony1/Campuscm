<?php
$pageTitle = 'Tableau de bord';
require_once  '../includes/admin-header.php';

?>

<h2 class="fw-bold mb-1">Bonjour, <?= e(current_user()['nom']) ?> 👋</h2>
<p class="text-muted mb-4">Voici un aperçu de la vitrine de <strong><?= e($ecole['nom']) ?></strong>.</p>

<?php if ($ecole['statut'] === 'en_attente'): ?>
  <div class="alert alert-warning"><i class="bi bi-hourglass-split"></i> Votre fiche est en attente de validation par notre équipe. Elle ne sera visible publiquement qu'après validation. Complétez-la en attendant !</div>
<?php elseif ($ecole['statut'] === 'rejete'): ?>
  <div class="alert alert-danger"><i class="bi bi-x-circle"></i> Votre fiche a été rejetée. Merci de vérifier les informations fournies puis de contacter le support.</div>
<?php else: ?>
  <div class="alert alert-success"><i class="bi bi-check-circle"></i> Votre vitrine est publiée et visible par tous les visiteurs.</div>
<?php endif; ?>

<div class="row g-3 mb-4">
  <div class="col-md-3 col-6">
    <div class="card shadow-sm text-center py-3">
      <i class="bi bi-eye fs-3 text-primary"></i>
      <h3 class="fw-bold mt-2"><?= (int)$ecole['vues'] ?></h3>
      <p class="text-muted small mb-0">Vues de la vitrine</p>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card shadow-sm text-center py-3">
      <i class="bi bi-journal-bookmark fs-3 text-primary"></i>
      <h3 class="fw-bold mt-2"><?= (int)$nbFilieres ?></h3>
      <p class="text-muted small mb-0">Filières publiées</p>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card shadow-sm text-center py-3">
      <i class="bi bi-images fs-3 text-primary"></i>
      <h3 class="fw-bold mt-2"><?= (int)$nbPhotos ?></h3>
      <p class="text-muted small mb-0">Photos en galerie</p>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card shadow-sm text-center py-3">
      <i class="bi bi-star fs-3 text-primary"></i>
      <h3 class="fw-bold mt-2"><?= (int)$avisData['n'] ?></h3>
      <p class="text-muted small mb-0">Avis reçus<?= $avisData['n'] ? ' (' . round($avisData['moy'],1) . '/5)' : '' ?></p>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="fw-bold mb-3">Prochaines étapes</h5>
    <ul class="list-group list-group-flush">
      <li class="list-group-item d-flex justify-content-between align-items-center">
        Compléter le profil et ajouter un logo / photo de couverture
        <a href="edit-profil.php" class="btn btn-sm btn-outline-primary">Modifier</a>
      </li>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        Ajouter vos filières de formation
        <a href="filieres.php" class="btn btn-sm btn-outline-primary">Gérer</a>
      </li>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        Ajouter des photos de votre établissement
        <a href="photos.php" class="btn btn-sm btn-outline-primary">Ajouter</a>
      </li>
    </ul>
  </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
