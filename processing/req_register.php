<?php
require_once 'includes/mailer.php';

if (is_logged_in()) {
    redirect(current_user()['role'] === 'super_admin' ? 'superadmin/dashboard.php' : 'admin/dashboard');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $nomEcole = trim($_POST['nom_ecole'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $domaine = trim($_POST['domaine'] ?? '');
    $nomAdmin = trim($_POST['nom_admin'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if ($nomEcole === '') $errors[] = "Le nom de l'école est requis.";
    if ($nomAdmin === '') $errors[] = "Votre nom est requis.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";
    if (strlen($password) < 6) $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    if ($password !== $passwordConfirm) $errors[] = "Les mots de passe ne correspondent pas.";

    $pdo = getPDO();
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT COUNT(*) AS n FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        if ((int)$stmt->fetch()['n'] > 0) {
            $errors[] = "Cet email est déjà utilisé.";
        }
    }

    if (empty($errors)) {
        // On ne crée PAS encore le compte : on stocke les infos en attente
        // de vérification, et on envoie un code par email.
        $code = generate_verification_code();
        $expiration = date('Y-m-d H:i:s', time() + VERIFICATION_CODE_DUREE_MINUTES * 60);

        $payload = json_encode([
            'nom_ecole' => $nomEcole,
            'ville' => $ville,
            'domaine' => $domaine ?: 'Formation professionnelle',
            'nom_admin' => $nomAdmin,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        // Un seul enregistrement en attente par email (remplace le précédent
        // si l'utilisateur relance une inscription avant d'avoir validé).
        $upsert = $pdo->prepare("
            INSERT INTO verifications_email (email, code, payload, tentatives, date_expiration)
            VALUES (?, ?, ?, 0, ?)
            ON CONFLICT(email) DO UPDATE SET
                code = excluded.code,
                payload = excluded.payload,
                tentatives = 0,
                date_creation = datetime('now'),
                date_expiration = excluded.date_expiration
        ");
        $upsert->execute([$email, $code, $payload, $expiration]);

        if (send_verification_email($email, $nomAdmin, $code)) {
            redirect('verify-email?email=' . urlencode($email));
        } else {
            $errors[] = "Impossible d'envoyer l'email de vérification. Vérifiez la configuration MAIL_DRIVER dans includes/config.php, ou réessayez.";
        }
    }
}