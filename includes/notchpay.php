<?php
/**
 * Intégration Notch Pay (Mobile Money : MTN MoMo, Orange Money, cartes)
 * Documentation officielle : https://developer.notchpay.co/
 *
 * On utilise l'API REST directement en cURL (pas de dépendance Composer),
 * ce qui fonctionne sur n'importe quel hébergement mutualisé.
 */

require_once __DIR__ . '/config.php';

/**
 * Effectue un appel HTTP vers l'API Notch Pay.
 */
function notchpay_request(string $method, string $endpoint, array $data = []): array
{
    $ch = curl_init(NOTCHPAY_API_BASE . $endpoint);

    $headers = [
        'Authorization: ' . NOTCHPAY_PUBLIC_KEY,
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // ne jamais désactiver la vérification SSL

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        return ['success' => false, 'message' => 'Erreur réseau : ' . $error];
    }

    $decoded = json_decode($response, true);

    if ($httpCode >= 200 && $httpCode < 300) {
        return ['success' => true, 'data' => $decoded];
    }

    return [
        'success' => false,
        'message' => $decoded['message'] ?? 'Erreur Notch Pay (HTTP ' . $httpCode . ')',
        'raw' => $decoded,
    ];
}

/**
 * Initialise un paiement et retourne l'URL de paiement (authorization_url)
 * vers laquelle rediriger l'utilisateur (choix MTN MoMo / Orange Money / carte).
 */
function notchpay_initialize_payment(array $params): array
{
    $payload = [
        'amount' => $params['amount'],
        'email' => $params['email'],
        'currency' => 'XAF',
        'callback' => $params['callback'],
        'reference' => $params['reference'],
        'description' => $params['description'] ?? '',
    ];
    if (!empty($params['phone'])) {
        $payload['phone'] = $params['phone'];
    }

    return notchpay_request('POST', '/payments/initialize', $payload);
}

/**
 * Vérifie le statut réel d'un paiement auprès de Notch Pay (source de vérité).
 * Ne jamais faire confiance au seul retour navigateur : toujours revérifier côté serveur.
 */
function notchpay_verify_payment(string $reference): array
{
    return notchpay_request('GET', '/payments/' . rawurlencode($reference));
}

/**
 * Vérifie la signature HMAC-SHA256 d'un webhook Notch Pay (header x-notch-signature).
 */
function notchpay_verify_webhook_signature(string $rawPayload, ?string $signature): bool
{
    if (empty($signature) || empty(NOTCHPAY_WEBHOOK_HASH) || NOTCHPAY_WEBHOOK_HASH === 'votre_hash_webhook') {
        return false;
    }
    $expected = hash_hmac('sha256', $rawPayload, NOTCHPAY_WEBHOOK_HASH);
    return hash_equals($expected, $signature);
}

/**
 * Génère une référence de transaction unique et lisible.
 */
function notchpay_generate_reference(int $ecoleId): string
{
    return 'campuscm_' . $ecoleId . '_' . time() . '_' . bin2hex(random_bytes(3));
}
