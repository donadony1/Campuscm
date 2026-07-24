<?php
if (is_logged_in()) {
    redirect(current_user()['role'] === 'super_admin' ? 'superadmin/dashboard.php' : 'admin/dashboard.php');
}

$email = trim($_GET['email'] ?? $_POST['email'] ?? '');
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect('mot-de-passe-oublie.php');
}

$pdo = getPDO();
$errors = [];
$success = null;

// Renvoi du code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['renvoyer'])) {
    csrf_verify();

    $stmt = $pdo->prepare('SELECT * FROM reinitialisations_mdp WHERE email = ?');
    $stmt->execute([$email]);
    $pending = $stmt->fetch();

    if (!$pending) {
        // Pas de demande en cours : on en recrée une silencieusement si le compte existe.
        redirect('mot-de-passe-oublie');
    }

    $secondesEcoulees = time() - strtotime($pending['date_creation']);
    if ($secondesEcoulees < PASSWORD_RESET_COOLDOWN_SECONDES) {
        $attente = PASSWORD_RESET_COOLDOWN_SECONDES - $secondesEcoulees;
        $errors[] = "Merci de patienter encore $attente seconde(s) avant de redemander un code.";
    } else {
        $stmtUser = $pdo->prepare('SELECT nom FROM utilisateurs WHERE email = ?');
        $stmtUser->execute([$email]);
        $user = $stmtUser->fetch();

        if ($user) {
            $nouveauCode = generate_verification_code();
            $maintenant = date('Y-m-d H:i:s');
            $nouvelleExpiration = date('Y-m-d H:i:s', time() + PASSWORD_RESET_DUREE_MINUTES * 60);

            $pdo->prepare('UPDATE reinitialisations_mdp SET code = ?, tentatives = 0, date_creation = ?, date_expiration = ? WHERE email = ?')
                ->execute([$nouveauCode, $maintenant, $nouvelleExpiration, $email]);

            send_password_reset_email($email, $user['nom'], $nouveauCode);
        }
        $success = 'Si un compte existe avec cette adresse, un nouveau code vient de lui être envoyé.';
    }
}

// Vérification du code + changement de mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reinitialiser'])) {
    csrf_verify();
    $codeSaisi = trim($_POST['code'] ?? '');
    $nouveauMdp = $_POST['password'] ?? '';
    $confirmMdp = $_POST['password_confirm'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM reinitialisations_mdp WHERE email = ?');
    $stmt->execute([$email]);
    $pending = $stmt->fetch();

    if (!$pending) {
        $errors[] = 'Aucune demande de réinitialisation en cours pour cet email. Merci de recommencer.';
    } elseif (strlen($nouveauMdp) < 6) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif ($nouveauMdp !== $confirmMdp) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    } elseif (strtotime($pending['date_expiration']) < time()) {
        $errors[] = "Ce code a expiré. Merci d'en demander un nouveau.";
    } elseif ($pending['tentatives'] >= 5) {
        $errors[] = 'Trop de tentatives incorrectes. Merci de redemander un nouveau code.';
    } elseif (!hash_equals($pending['code'], $codeSaisi)) {
        $pdo->prepare('UPDATE reinitialisations_mdp SET tentatives = tentatives + 1 WHERE email = ?')->execute([$email]);
        $errors[] = 'Code incorrect. Il vous reste ' . (4 - $pending['tentatives']) . ' tentative(s).';
    } else {
        $hash = password_hash($nouveauMdp, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE utilisateurs SET mot_de_passe = ? WHERE email = ?')->execute([$hash, $email]);
        $pdo->prepare('DELETE FROM reinitialisations_mdp WHERE email = ?')->execute([$email]);

        set_flash('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez vous connecter.');
        redirect('login');
    }
}