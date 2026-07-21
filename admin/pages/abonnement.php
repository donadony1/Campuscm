<?php
$pageTitle = 'Abonnement';
require_once '../includes/admin-header.php';
require_once '../includes/notchpay.php';

?>

<h2 class="fw-bold mb-4">Abonnement</h2>

<div class="row g-4">
  <div class="col-md-6">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <h5 class="fw-bold">Plan Gratuit</h5>
        <p class="text-muted small">Vitrine standard, visible dans les résultats de recherche.</p>
        <h3 class="fw-bold">0 FCFA</h3>
        <ul class="list-unstyled small text-muted mt-3">
          <li><i class="bi bi-check text-success"></i> Fiche complète (profil, filières, photos)</li>
          <li><i class="bi bi-check text-success"></i> Réception d'avis visiteurs</li>
          <li><i class="bi bi-dash text-muted"></i> Pas de mise en avant</li>
          <li><i class="bi bi-dash text-muted"></i> Pas de badge "Vérifié"</li>
        </ul>
        <?php if (!$estPremium): ?>
          <span class="badge bg-secondary">Plan actuel</span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-md-6">
    <div class="card shadow-sm h-100 border-warning">
      <div class="card-body">
        <h5 class="fw-bold text-warning-emphasis"><i class="bi bi-star-fill text-warning"></i> Vitrine Premium</h5>
        <p class="text-muted small">Mise en avant sur l'accueil et la recherche, badge vérifié, priorité d'affichage.</p>
        <h3 class="fw-bold"><?= number_format(PLAN_PREMIUM_PRIX_MENSUEL, 0, ',', ' ') ?> FCFA <small class="fs-6 text-muted fw-normal">/ mois</small></h3>
        <ul class="list-unstyled small text-muted mt-3">
          <li><i class="bi bi-check text-success"></i> Tout le plan Gratuit</li>
          <li><i class="bi bi-check text-success"></i> Badge <span class="badge bg-warning text-dark">Vérifié</span></li>
          <li><i class="bi bi-check text-success"></i> Priorité dans les résultats de recherche</li>
          <li><i class="bi bi-check text-success"></i> Mise en avant sur la page d'accueil</li>
        </ul>

        <?php if ($estPremium): ?>
          <div class="alert alert-success py-2 small mb-0">
            <i class="bi bi-check-circle"></i> Actif jusqu'au <?= e(date('d/m/Y', strtotime($ecole['premium_jusqu_au']))) ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="card-footer bg-white border-0">
        <button type="button" class="btn btn-warning w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalPaiement">
          <i class="bi bi-phone"></i> <?= $estPremium ? 'Renouveler' : 'Passer au Premium' ?> — Payer par Mobile Money
        </button>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm mt-4">
  <div class="card-body">
    <h5 class="fw-bold mb-3">Historique des paiements</h5>
    <?php if (empty($historique)): ?>
      <p class="text-muted mb-0">Aucun paiement effectué pour le moment.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead><tr><th>Référence</th><th>Montant</th><th>Statut</th><th>Date</th></tr></thead>
          <tbody>
          <?php foreach ($historique as $p): ?>
            <tr>
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

<!-- Modal paiement -->
<div class="modal fade" id="modalPaiement" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div class="modal-header">
          <h5 class="modal-title">Paiement Mobile Money</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted small">Vous serez redirigé vers Notch Pay pour choisir MTN Mobile Money, Orange Money ou carte bancaire.</p>
          <div class="mb-3">
            <label class="form-label">Email (reçu de paiement)</label>
            <input type="email" name="email" class="form-control" value="<?= e($ecole['email']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Numéro Mobile Money</label>
            <input type="text" name="telephone" class="form-control" value="<?= e($ecole['telephone']) ?>" placeholder="+237 6XX XXX XXX">
          </div>
          <div class="alert alert-secondary small mb-0">
            Montant à payer : <strong><?= number_format(PLAN_PREMIUM_PRIX_MENSUEL, 0, ',', ' ') ?> FCFA</strong>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" name="payer" class="btn btn-warning fw-semibold">Continuer vers le paiement</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once  '../includes/admin-footer.php'; ?>
