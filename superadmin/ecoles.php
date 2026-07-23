<?php
$pageTitle = 'Gestion des écoles';
require_once __DIR__ . '/../includes/superadmin-header.php';

$pdo = getPDO();

// Changement de statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_statut'])) {
    csrf_verify();
    $id = (int)$_POST['ecole_id'];
    $nouveauStatut = $_POST['action_statut'];
    if (in_array($nouveauStatut, ['valide', 'rejete', 'en_attente'], true)) {
        $pdo->prepare('UPDATE ecoles SET statut = ? WHERE id = ?')->execute([$nouveauStatut, $id]);
        set_flash('success', 'Statut mis à jour.');
    }
    redirect('ecoles.php' . (isset($_GET['statut']) ? '?statut=' . urlencode($_GET['statut']) : ''));
}

// Suppression définitive
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare('DELETE FROM ecoles WHERE id = ?')->execute([$id]);
    set_flash('success', 'École supprimée définitivement.');
    redirect('ecoles.php');
}

$filtreStatut = $_GET['statut'] ?? '';
$sql = 'SELECT * FROM ecoles';
$params = [];
if ($filtreStatut !== '') {
    $sql .= ' WHERE statut = ?';
    $params[] = $filtreStatut;
}
$sql .= ' ORDER BY date_creation DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ecoles = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="fw-bold mb-0">Gestion des écoles</h2>
  <div class="btn-group">
    <a href="ecoles.php" class="btn btn-sm btn-outline-secondary <?= $filtreStatut === '' ? 'active' : '' ?>">Toutes</a>
    <a href="ecoles.php?statut=en_attente" class="btn btn-sm btn-outline-warning <?= $filtreStatut === 'en_attente' ? 'active' : '' ?>">En attente</a>
    <a href="ecoles.php?statut=valide" class="btn btn-sm btn-outline-success <?= $filtreStatut === 'valide' ? 'active' : '' ?>">Validées</a>
    <a href="ecoles.php?statut=rejete" class="btn btn-sm btn-outline-danger <?= $filtreStatut === 'rejete' ? 'active' : '' ?>">Rejetées</a>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <?php if (empty($ecoles)): ?>
      <p class="text-muted mb-0">Aucune école dans cette catégorie.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead><tr><th>École</th><th>Ville</th><th>Domaine</th><th>Statut</th><th>Vues</th><th class="text-end">Actions</th></tr></thead>
          <tbody>
          <?php foreach ($ecoles as $e): ?>
            <tr>
              <td class="fw-semibold"><?= e($e['nom']) ?></td>
              <td><?= e($e['ville']) ?></td>
              <td><?= e($e['domaine']) ?></td>
              <td>
                <?php $badges = ['valide' => 'success', 'en_attente' => 'warning', 'rejete' => 'danger']; ?>
                <span class="badge bg-<?= $badges[$e['statut']] ?? 'secondary' ?>"><?= e($e['statut']) ?></span>
              </td>
              <td><?= (int)$e['vues'] ?></td>
              <td class="text-end">
                <a href="../ecole?slug=<?= e($e['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Voir"><i class="bi bi-eye"></i></a>

                <?php if ($e['statut'] !== 'valide'): ?>
                  <form method="post" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="ecole_id" value="<?= $e['id'] ?>">
                    <button type="submit" name="action_statut" value="valide" class="btn btn-sm btn-outline-success" title="Valider"><i class="bi bi-check-lg"></i></button>
                  </form>
                <?php endif; ?>

                <?php if ($e['statut'] !== 'rejete'): ?>
                  <form method="post" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="ecole_id" value="<?= $e['id'] ?>">
                    <button type="submit" name="action_statut" value="rejete" class="btn btn-sm btn-outline-warning" title="Rejeter"><i class="bi bi-x-lg"></i></button>
                  </form>
                <?php endif; ?>

                <a href="ecoles.php?delete=<?= $e['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Supprimer définitivement cette école et toutes ses données (filières, photos, avis) ?" title="Supprimer">
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

<?php require_once __DIR__ . '/../includes/superadmin-footer.php'; ?>
