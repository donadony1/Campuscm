<?php
$pageTitle = "Vue d'ensemble";
require_once __DIR__ . '/../includes/superadmin-header.php';

$pdo = getPDO();

$total = $pdo->query('SELECT COUNT(*) AS n FROM ecoles')->fetch()['n'];
$valides = $pdo->query("SELECT COUNT(*) AS n FROM ecoles WHERE statut='valide'")->fetch()['n'];
$enAttente = $pdo->query("SELECT COUNT(*) AS n FROM ecoles WHERE statut='en_attente'")->fetch()['n'];
$rejetees = $pdo->query("SELECT COUNT(*) AS n FROM ecoles WHERE statut='rejete'")->fetch()['n'];
$totalVues = $pdo->query('SELECT SUM(vues) AS n FROM ecoles')->fetch()['n'] ?? 0;

$dernieres = $pdo->query('SELECT * FROM ecoles ORDER BY date_creation DESC LIMIT 8')->fetchAll();
?>

<h2 class="fw-bold mb-4">Vue d'ensemble de la plateforme</h2>

<div class="row g-3 mb-4">
  <div class="col-md-3 col-6">
    <div class="card shadow-sm text-center py-3">
      <i class="bi bi-building fs-3 text-primary"></i>
      <h3 class="fw-bold mt-2"><?= (int)$total ?></h3>
      <p class="text-muted small mb-0">Écoles inscrites</p>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card shadow-sm text-center py-3">
      <i class="bi bi-check-circle fs-3 text-success"></i>
      <h3 class="fw-bold mt-2"><?= (int)$valides ?></h3>
      <p class="text-muted small mb-0">Validées</p>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card shadow-sm text-center py-3">
      <i class="bi bi-hourglass-split fs-3 text-warning"></i>
      <h3 class="fw-bold mt-2"><?= (int)$enAttente ?></h3>
      <p class="text-muted small mb-0">En attente</p>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card shadow-sm text-center py-3">
      <i class="bi bi-eye fs-3 text-primary"></i>
      <h3 class="fw-bold mt-2"><?= (int)$totalVues ?></h3>
      <p class="text-muted small mb-0">Vues cumulées</p>
    </div>
  </div>
</div>

<?php if ($enAttente > 0): ?>
<div class="alert alert-warning d-flex justify-content-between align-items-center">
  <span><i class="bi bi-hourglass-split"></i> <?= (int)$enAttente ?> école(s) en attente de validation.</span>
  <a href="ecoles.php?statut=en_attente" class="btn btn-sm btn-warning">Traiter maintenant</a>
</div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="fw-bold mb-3">Dernières inscriptions</h5>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead><tr><th>École</th><th>Ville</th><th>Statut</th><th>Date</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($dernieres as $e): ?>
          <tr>
            <td class="fw-semibold"><?= e($e['nom']) ?></td>
            <td><?= e($e['ville']) ?></td>
            <td>
              <?php $badges = ['valide' => 'success', 'en_attente' => 'warning', 'rejete' => 'danger']; ?>
              <span class="badge bg-<?= $badges[$e['statut']] ?? 'secondary' ?>"><?= e($e['statut']) ?></span>
            </td>
            <td class="small text-muted"><?= e($e['date_creation']) ?></td>
            <td><a href="ecoles.php" class="btn btn-sm btn-outline-primary">Gérer</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/superadmin-footer.php'; ?>
