<?php


$ecoleId = current_user()['ecole_id'];

$stmt = $pdo->prepare('SELECT * FROM ecoles WHERE id = ?');
$stmt->execute([$ecoleId]);
$ecole = $stmt->fetch();

$estPremium = is_premium_active($ecole['premium_jusqu_au']);

// Lancement d'un paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payer'])) {
    csrf_verify();

    $email = trim($_POST['email'] ?? $ecole['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? $ecole['telephone'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('error', 'Merci de renseigner un email valide pour recevoir la confirmation de paiement.');
        redirect('abonnement');
    }

    $reference = notchpay_generate_reference((int)$ecoleId);

    // On enregistre la tentative de paiement AVANT l'appel API (statut en_attente)
    $ins = $pdo->prepare("INSERT INTO paiements (ecole_id, reference, montant, devise, plan, statut) VALUES (?, ?, ?, 'XAF', 'premium', 'en_attente')");
    $ins->execute([$ecoleId, $reference, PLAN_PREMIUM_PRIX_MENSUEL]);

    $callbackUrl = rtrim(APP_URL, '/') . '/admin/paiement-retour.php?reference=' . urlencode($reference);

    $result = notchpay_initialize_payment([
        'amount' => PLAN_PREMIUM_PRIX_MENSUEL,
        'email' => $email,
        'phone' => $telephone,
        'reference' => $reference,
        'callback' => $callbackUrl,
        'description' => 'Abonnement Vitrine Premium - ' . $ecole['nom'],
    ]);

    if ($result['success'] && !empty($result['data']['authorization_url'])) {
        redirect($result['data']['authorization_url']);
    } else {
        $pdo->prepare("UPDATE paiements SET statut = 'echoue', date_maj = datetime('now') WHERE reference = ?")->execute([$reference]);
        set_flash('error', "Impossible d'initier le paiement : " . ($result['message'] ?? 'erreur inconnue') . '. Vérifiez vos clés Notch Pay dans includes/config.php.');
        redirect('abonnement.php');
    }
}

$historique = $pdo->prepare('SELECT * FROM paiements WHERE ecole_id = ? ORDER BY date_creation DESC LIMIT 10');
$historique->execute([$ecoleId]);
$historique = $historique->fetchAll();