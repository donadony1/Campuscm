<?php
$pageTitle = 'Paiements';
require_once __DIR__ . '/../includes/superadmin-header.php';

$pdo = getPDO();

$totalComplete = $pdo->query("SELECT COALESCE(SUM(montant),0) AS n FROM paiements WHERE statut='complete'")->fetch()['n'];
$nbComplete = $pdo->query("SELECT COUNT(*) AS n FROM paiements WHERE statut='complete'")->fetch()['n'];
$nbEnAttente = $pdo->query("SELECT COUNT(*) AS n FROM paiements WHERE statut='en_attente'")->fetch()['n'];

$paiements = $pdo->query("
    SELECT p.*, e.nom AS ecole_nom
    FROM paiements p
    JOIN ecoles e ON e.id = p.ecole_id
    ORDER BY p.date_creation DESC
    LIMIT 100
")->fetchAll();
?>

<h2 class="fw-bold mb-4">Paiements</h2>

<div class="row g-3 mb-4">
  <div class="col-md-4 col-6">
    <div class="card shadow-sm text-center py-3">
      <i class="bi bi-cash-stack fs-3 text-success"></i>
      <h3 class="fw-bold mt-2"><?= number_format($totalComplete, 0, ',', ' ') ?> FCFA</h3>
      <p class="text-muted small mb-0">Revenus confirmés</p>
    </div>
  </div>
  <div class="col-md-4 col-6">
    <div class="card shadow-sm text-center py-3">
      <i class="bi bi-check-circle fs-3 text-success"></i>
      <h3 class="fw-bold mt-2"><?= (int)$nbComplete ?></h3>
      <p class="text-muted small mb-0">Paiements complétés</p>
    </div>
  </div>
  <div class="col-md-4 col-6">
    <div class="card shadow-sm text-center py-3">
      <i class="bi bi-hourglass-split fs-3 text-warning"></i>
      <h3 class="fw-bold mt-2"><?= (int)$nbEnAttente ?></h3>
      <p class="text-muted small mb-0">En attente</p>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <?php if (empty($paiements)): ?>
      <p class="text-muted mb-0">Aucun paiement pour le moment.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead><tr><th>École</th><th>Référence</th><th>Montant</th><th>Statut</th><th>Date</th></tr></thead>
          <tbody>
          <?php foreach ($paiements as $p): ?>
            <tr>
              <td class="fw-semibold"><?= e($p['ecole_nom']) ?></td>
              <td class="small font-monospace"><?= e($p['reference']) ?></td>
              <td><?= number_format($p['montant'], 0, ',', ' ') ?> <?= e($p['devise']) ?></td>
              <td>
                <?php $badges = ['complete' => 'success', 'en_attente' => 'warning', 'echoue' => 'danger']; ?>
                <span class="badge bg-<?= $badges[$p['statut']] ?? 'secondary' ?>"><?= e($p['statut']) ?></span>
              </td>
              <td class="small text-muted"><?= e($p['date_creation']) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/superadmin-footer.php'; ?>
