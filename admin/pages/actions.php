<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in() || current_user()['role'] !== 'admin_ecole') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Non autorisé.']);
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Jeton de sécurité invalide.']);
    exit;
}

$pdo = getPDO();
$ecoleId = current_user()['ecole_id'];
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'delete_photo':
        $id = (int)($_POST['id'] ?? 0);
        // Vérifie que la photo appartient bien à l'école connectée avant suppression
        $stmt = $pdo->prepare('SELECT * FROM medias WHERE id = ? AND ecole_id = ?');
        $stmt->execute([$id, $ecoleId]);
        $photo = $stmt->fetch();

        if (!$photo) {
            echo json_encode(['success' => false, 'message' => 'Photo introuvable.']);
            exit;
        }

        $filePath = UPLOAD_DIR_PHOTOS . $photo['chemin'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
        $pdo->prepare('DELETE FROM medias WHERE id = ?')->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action inconnue.']);
}
