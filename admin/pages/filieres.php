<?php
$pageTitle = 'Filières';
require_once '../includes/admin-header.php';

?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="fw-bold mb-0">Filières & Formations</h2>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalFiliere" onclick="resetForm()">
    <i class="bi bi-plus-lg"></i> Ajouter une filière
  </button>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <?php if (empty($filieres)): ?>
      <p class="text-muted mb-0">Aucune filière ajoutée. Cliquez sur "Ajouter une filière" pour commencer.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead><tr><th>Nom</th><th>Niveau</th><th>Durée</th><th>Prix</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($filieres as $f): ?>
            <tr>
              <td class="fw-semibold"><?= e($f['nom']) ?></td>
              <td><?= e($f['niveau']) ?></td>
              <td><?= e($f['duree']) ?></td>
              <td><?= e($f['prix']) ?></td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-secondary"
                  onclick='editFiliere(<?= json_encode($f, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                  <i class="bi bi-pencil"></i>
                </button>
                <a href="filieres.php?delete=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Supprimer cette filière ?">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal ajout/édition -->
<div class="modal fade" id="modalFiliere" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="filiere_id" id="filiere_id" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Ajouter une filière</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nom de la filière *</label>
            <input type="text" name="nom" id="f_nom" class="form-control" required>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Niveau</label>
              <input type="text" name="niveau" id="f_niveau" class="form-control" placeholder="BTS, Licence, Certificat...">
            </div>
            <div class="col-md-6">
              <label class="form-label">Durée</label>
              <input type="text" name="duree" id="f_duree" class="form-control" placeholder="2 ans, 6 mois...">
            </div>
          </div>
          <div class="mb-3 mt-3">
            <label class="form-label">Prix</label>
            <input type="text" name="prix" id="f_prix" class="form-control" placeholder="Ex: 250 000 FCFA / an">
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" id="f_description" rows="3" class="form-control"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function resetForm() {
  document.getElementById('modalTitle').textContent = 'Ajouter une filière';
  document.getElementById('filiere_id').value = '';
  document.getElementById('f_nom').value = '';
  document.getElementById('f_niveau').value = '';
  document.getElementById('f_duree').value = '';
  document.getElementById('f_prix').value = '';
  document.getElementById('f_description').value = '';
}
function editFiliere(f) {
  document.getElementById('modalTitle').textContent = 'Modifier la filière';
  document.getElementById('filiere_id').value = f.id;
  document.getElementById('f_nom').value = f.nom;
  document.getElementById('f_niveau').value = f.niveau || '';
  document.getElementById('f_duree').value = f.duree || '';
  document.getElementById('f_prix').value = f.prix || '';
  document.getElementById('f_description').value = f.description || '';
  new bootstrap.Modal(document.getElementById('modalFiliere')).show();
}
</script>

<?php require_once '../includes/admin-footer.php'; ?>
