<?php
/**
 * Webhook Notch Pay — à configurer dans le dashboard Notch Pay :
 *   https://votredomaine.cm/webhook-notchpay.php
 *
 * Notch Pay appelle cette URL en arrière-plan (serveur à serveur) dès qu'un
 * paiement change de statut. C'est la méthode FIABLE de confirmation :
 * contrairement au retour navigateur, elle fonctionne même si l'utilisateur
 * ferme l'onglet avant la fin du paiement Mobile Money.
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/notchpay.php';

header('Content-Type: application/json');

$rawPayload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_NOTCH_SIGNATURE'] ?? null;

// Vérification obligatoire de la signature HMAC-SHA256 : sans elle, n'importe
// qui pourrait appeler cette URL et activer un abonnement premium gratuitement.
if (!notchpay_verify_webhook_signature($rawPayload, $signature)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Signature invalide.']);
    exit;
}

$event = json_decode($rawPayload, true);
if (!$event || empty($event['event'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Payload invalide.']);
    exit;
}

$pdo = getPDO();
$reference = $event['data']['reference'] ?? null;

if ($event['event'] === 'payment.complete' && $reference) {
    $stmt = $pdo->prepare('SELECT ecole_id FROM paiements WHERE reference = ?');
    $stmt->execute([$reference]);
    $ecoleId = $stmt->fetchColumn();

    if ($ecoleId) {
        activer_premium_ecole($pdo, (int)$ecoleId, $reference);
    }
} elseif ($event['event'] === 'payment.failed' && $reference) {
    $pdo->prepare("UPDATE paiements SET statut = 'echoue', date_maj = datetime('now') WHERE reference = ? AND statut != 'complete'")->execute([$reference]);
}

// Toujours répondre 200 rapidement pour éviter que Notch Pay ne considère l'appel en échec.
http_response_code(200);
echo json_encode(['success' => true]);
