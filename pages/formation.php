<?php
$pageTitle = $formation['nom'];
require_once 'includes/header.php';

$telephoneEcole = preg_replace('/\D/', '', $formation['ecole_telephone'] ?? '');
?>

<div class="container py-5">
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb small">
      <li class="breadcrumb-item"><a href="home">Accueil</a></li>
      <li class="breadcrumb-item"><a href="formations">Formations</a></li>
      <li class="breadcrumb-item active"><?= e($formation['nom']) ?></li>
    </ol>
  </nav>

  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card shadow-sm mb-4">
        <img src="<?= $formation['image'] ? UPLOAD_URL_FORMATIONS . e($formation['image']) : 'https://placehold.co/800x350?text=' . urlencode($formation['nom']) ?>"
             class="card-img-top" style="max-height:350px;object-fit:cover;" alt="<?= e($formation['nom']) ?>">
        <div class="card-body">
          <?php if ($formation['ecole_plan'] === 'premium'): ?>
            <span class="badge bg-warning text-dark mb-2"><i class="bi bi-patch-check-fill"></i> École vérifiée</span>
          <?php endif; ?>
          <h1 class="fw-bold h3"><?= e($formation['nom']) ?></h1>
          <p class="text-muted mb-3">
            <i class="bi bi-building"></i> <a href="ecole?slug=<?= e($formation['ecole_slug']) ?>" class="text-decoration-none"><?= e($formation['ecole_nom']) ?></a>
            <?php if ($formation['ecole_ville']): ?> · <i class="bi bi-geo-alt"></i> <?= e($formation['ecole_ville']) ?><?php endif; ?>
          </p>

          <div class="d-flex flex-wrap gap-2 mb-3">
            <?php if ($formation['niveau']): ?><span class="badge bg-primary-subtle text-primary"><i class="bi bi-mortarboard"></i> <?= e($formation['niveau']) ?></span><?php endif; ?>
            <?php if ($formation['duree']): ?><span class="badge bg-light text-dark border"><i class="bi bi-clock"></i> <?= e($formation['duree']) ?></span><?php endif; ?>
            <?php if ($formation['prix']): ?><span class="badge bg-light text-dark border"><i class="bi bi-cash-coin"></i> <?= e($formation['prix']) ?></span><?php endif; ?>
          </div>

          <h5 class="fw-bold mt-4">Description</h5>
          <p style="white-space: pre-line;"><?= e($formation['description'] ?: 'Aucune description fournie pour cette formation.') ?></p>
        </div>
      </div>

      <?php if (!empty($autresFormations)): ?>
      <div class="card shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Autres formations de <?= e($formation['ecole_nom']) ?></h6>
          <div class="list-group list-group-flush">
            <?php foreach ($autresFormations as $af): ?>
              <a href="formation?id=<?= $af['id'] ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
                <img src="<?= $af['image'] ? UPLOAD_URL_FORMATIONS . e($af['image']) : 'https://placehold.co/60x60?text=+' ?>"
                     class="rounded" style="width:45px;height:45px;object-fit:cover;">
                <span><?= e($af['nom']) ?><br><small class="text-muted"><?= e($af['niveau']) ?></small></span>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <div class="col-lg-5">
      <div class="card shadow-sm sticky-top" style="top: 90px;">
        <div class="card-body text-center">
          <i class="bi bi-send-check display-5 text-primary"></i>
          <h5 class="fw-bold mt-2">Intéressé(e) par cette formation ?</h5>
          <p class="text-muted small">Laissez vos coordonnées, vous serez redirigé(e) vers WhatsApp pour contacter directement l'établissement.</p>

          <?php if ($telephoneEcole): ?>
            <button type="button" class="btn btn-success btn-lg w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalContact">
              <i class="bi bi-whatsapp"></i> Je suis intéressé(e)
            </button>
          <?php else: ?>
            <div class="alert alert-secondary small mb-0">Cet établissement n'a pas encore renseigné de numéro de contact.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <?php if (!empty($formationsSuggerees)): ?>
  <div class="mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="fw-bold mb-0">Ça pourrait aussi vous intéresser</h4>
      <a href="formations" class="btn btn-outline-primary btn-sm">Voir toutes les formations <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-3">
      <?php foreach ($formationsSuggerees as $f): ?>
        <?php include 'includes/formation-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- Modal formulaire de contact -->
<div class="modal fade" id="modalContact" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Vos coordonnées</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small">Ces informations seront simplement utilisées pour préparer votre message WhatsApp à l'établissement.</p>
        <form id="formContactWhatsapp">
          <div class="mb-3">
            <label class="form-label">Votre nom complet *</label>
            <input type="text" id="contact_nom" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Votre numéro de téléphone *</label>
            <input type="text" id="contact_telephone" class="form-control" placeholder="+237 6XX XXX XXX" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Un message (facultatif)</label>
            <textarea id="contact_message" class="form-control" rows="2" placeholder="Ex: Je souhaite plus d'informations sur les conditions d'admission."></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" id="btnEnvoyerWhatsapp" class="btn btn-success fw-semibold">
          <i class="bi bi-whatsapp"></i> Continuer vers WhatsApp
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('btnEnvoyerWhatsapp')?.addEventListener('click', function () {
  const form = document.getElementById('formContactWhatsapp');
  if (!form.reportValidity()) {
    return;
  }

  const nom = document.getElementById('contact_nom').value.trim();
  const telephone = document.getElementById('contact_telephone').value.trim();
  const message = document.getElementById('contact_message').value.trim();

  const formationNom = <?= json_encode($formation['nom']) ?>;
  const ecoleNom = <?= json_encode($formation['ecole_nom']) ?>;
  const telephoneEcole = <?= json_encode($telephoneEcole) ?>;

  let texte = `Bonjour, je m'appelle ${nom} (${telephone}). Je suis intéressé(e) par la formation "${formationNom}" à ${ecoleNom}.`;
  if (message) {
    texte += ` ${message}`;
  }

  const url = `https://wa.me/${telephoneEcole}?text=${encodeURIComponent(texte)}`;
  window.open(url, '_blank');
});
</script>

<?php require_once 'includes/footer.php'; ?>
