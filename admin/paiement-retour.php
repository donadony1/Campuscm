<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/notchpay.php';

require_login();
if (current_user()['role'] !== 'admin_ecole') {
    http_response_code(403);
    die('Accès réservé.');
}

$reference = $_GET['reference'] ?? '';
$pdo = getPDO();
$ecoleId = current_user()['ecole_id'];

if ($reference === '') {
    set_flash('error', 'Référence de paiement manquante.');
    redirect('abonnement');
}

// Vérifie que ce paiement appartient bien à l'école connectée
$stmt = $pdo->prepare('SELECT * FROM paiements WHERE reference = ? AND ecole_id = ?');
$stmt->execute([$reference, $ecoleId]);
$paiement = $stmt->fetch();

if (!$paiement) {
    set_flash('error', 'Paiement introuvable.');
    redirect('abonnement');
}

// IMPORTANT : on ne fait jamais confiance au simple retour navigateur.
// On revérifie toujours le statut réel directement auprès de l'API Notch Pay.
$verif = notchpay_verify_payment($reference);

$statutNotchPay = $verif['data']['transaction']['status'] ?? $verif['data']['status'] ?? null;

if ($verif['success'] && $statutNotchPay === 'complete') {
    activer_premium_ecole($pdo, $ecoleId, $reference);
    set_flash('success', 'Paiement confirmé ! Votre vitrine est maintenant en Premium pour 30 jours.');
} elseif ($statutNotchPay === 'failed' || $statutNotchPay === 'canceled') {
    $pdo->prepare("UPDATE paiements SET statut = 'echoue', date_maj = datetime('now') WHERE reference = ?")->execute([$reference]);
    set_flash('error', 'Le paiement a échoué ou a été annulé. Vous pouvez réessayer.');
} else {
    // En attente (ex: paiement Mobile Money pas encore confirmé par l'opérateur).
    // Le webhook (webhook-notchpay.php) confirmera automatiquement dès réception.
    set_flash('success', 'Paiement en cours de traitement. Nous confirmerons automatiquement votre abonnement dès validation par l\'opérateur (quelques instants à quelques minutes).');
}

redirect('abonnement');
