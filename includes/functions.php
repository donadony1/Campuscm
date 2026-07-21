<?php

/** Échappement HTML raccourci */
function e(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/** Redirection + arrêt du script */
function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

/** Génère un slug URL-friendly à partir d'un nom d'école */
function slugify(string $text): string
{
    $text = mb_strtolower($text, 'UTF-8');
    $text = strtr($text, [
        'à' => 'a', 'â' => 'a', 'ä' => 'a', 'é' => 'e', 'è' => 'e', 'ê' => 'e',
        'ë' => 'e', 'î' => 'i', 'ï' => 'i', 'ô' => 'o', 'ö' => 'o', 'ù' => 'u',
        'û' => 'u', 'ü' => 'u', 'ç' => 'c', 'œ' => 'oe',
    ]);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

/** Garantit l'unicité d'un slug en base (ajoute -2, -3, ... si besoin) */
function slug_unique(PDO $pdo, string $base, ?int $excludeId = null): string
{
    $slug = slugify($base);
    $original = $slug;
    $i = 2;
    while (true) {
        $sql = 'SELECT COUNT(*) AS n FROM ecoles WHERE slug = ?';
        $params = [$slug];
        if ($excludeId) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        if ((int)$stmt->fetch()['n'] === 0) {
            return $slug;
        }
        $slug = $original . '-' . $i;
        $i++;
    }
}

/** Messages flash (succès / erreur) affichés une seule fois après redirection */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/** Jeton CSRF */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Jeton de sécurité invalide. Merci de recharger la page et de réessayer.');
    }
}

/**
 * Gère l'upload sécurisé d'une image. Retourne le nom de fichier généré
 * ou null si aucun fichier n'a été envoyé / erreur.
 */
function handle_image_upload(string $fieldName, string $destDir): ?string
{
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $file = $_FILES[$fieldName];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        set_flash('error', "Erreur lors de l'upload du fichier.");
        return null;
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        set_flash('error', 'Image trop volumineuse (max 3 Mo).');
        return null;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, ALLOWED_IMAGE_TYPES, true)) {
        set_flash('error', 'Format d\'image non autorisé (jpg, png, webp uniquement).');
        return null;
    }

    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => 'jpg',
    };

    $filename = uniqid('img_', true) . '.' . $ext;
    $dest = rtrim($destDir, '/') . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        set_flash('error', 'Impossible d\'enregistrer l\'image.');
        return null;
    }

    return $filename;
}

/** Vérifie si une école a un abonnement premium actif (non expiré) */
function is_premium_active(?string $premiumJusquAu): bool
{
    if (empty($premiumJusquAu)) {
        return false;
    }
    return strtotime($premiumJusquAu) > time();
}

/**
 * Rétrograde en 'gratuit' toute école dont le premium a expiré.
 * Écrit en PHP pur (pas de fonction date SQL) pour rester compatible
 * à la fois avec SQLite et MySQL. À appeler avant tout affichage public.
 */
function downgrade_expired_premiums(PDO $pdo): void
{
    $stmt = $pdo->prepare("UPDATE ecoles SET plan = 'gratuit' WHERE plan = 'premium' AND premium_jusqu_au IS NOT NULL AND premium_jusqu_au < ?");
    $stmt->execute([date('Y-m-d H:i:s')]);
}

/**
 * Active le plan premium pour une école (30 jours, prolongeable) et marque
 * le paiement comme complété. Idempotent : appelée par la fois la page de
 * retour utilisateur ET le webhook Notch Pay, sans double activation.
 */
function activer_premium_ecole(PDO $pdo, int $ecoleId, string $reference): void
{
    $stmt = $pdo->prepare('SELECT statut FROM paiements WHERE reference = ?');
    $stmt->execute([$reference]);
    $statutActuel = $stmt->fetchColumn();

    if ($statutActuel === 'complete') {
        return; // déjà traité, on ne prolonge pas deux fois
    }

    $pdo->prepare("UPDATE paiements SET statut = 'complete', date_maj = datetime('now') WHERE reference = ?")->execute([$reference]);

    $ecole = $pdo->prepare('SELECT premium_jusqu_au FROM ecoles WHERE id = ?');
    $ecole->execute([$ecoleId]);
    $actuel = $ecole->fetchColumn();

    // Si déjà premium et non expiré, on prolonge depuis la date d'expiration actuelle.
    $base = is_premium_active($actuel) ? strtotime($actuel) : time();
    $nouvelleDate = date('Y-m-d H:i:s', $base + PLAN_PREMIUM_DUREE_JOURS * 86400);

    $pdo->prepare("UPDATE ecoles SET plan = 'premium', premium_jusqu_au = ? WHERE id = ?")->execute([$nouvelleDate, $ecoleId]);
}

/** Formate une note moyenne d'avis en étoiles Bootstrap Icons */
function render_stars(float $note): string
{
    $note = round($note);
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $note ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-warning"></i>';
    }
    return $html;
}
