<?php
require_once  'includes/mailer.php';
if (is_logged_in()) {
    redirect(current_user()['role'] === 'super_admin' ? 'superadmin/dashboard.php' : 'admin/dashboard.php');
}

$email = trim($_GET['email'] ?? $_POST['email'] ?? '');
if ($email === '') {
    redirect('register.php');
}

$pdo = getPDO();
$errors = [];
$success = null;

// Renvoi du code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['renvoyer'])) {
    csrf_verify();

    $stmt = $pdo->prepare('SELECT * FROM verifications_email WHERE email = ?');
    $stmt->execute([$email]);
    $pending = $stmt->fetch();

    if (!$pending) {
        redirect('register.php');
    }

    $secondesEcoulees = time() - strtotime($pending['date_creation']);
    if ($secondesEcoulees < VERIFICATION_RESEND_COOLDOWN_SECONDES) {
        $attente = VERIFICATION_RESEND_COOLDOWN_SECONDES - $secondesEcoulees;
        $errors[] = "Merci de patienter encore $attente seconde(s) avant de redemander un code.";
    } else {
        $payload = json_decode($pending['payload'], true);
        $nouveauCode = generate_verification_code();
        $nouvelleExpiration = date('Y-m-d H:i:s', time() + VERIFICATION_CODE_DUREE_MINUTES * 60);

        $pdo->prepare("UPDATE verifications_email SET code = ?, tentatives = 0, date_creation = datetime('now'), date_expiration = ? WHERE email = ?")
            ->execute([$nouveauCode, $nouvelleExpiration, $email]);

        if (send_verification_email($email, $payload['nom_admin'], $nouveauCode)) {
            $success = "Un nouveau code vous a été envoyé.";
        } else {
            $errors[] = "Impossible d'envoyer l'email. Réessayez plus tard.";
        }
    }
}

// Vérification du code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verifier'])) {
    csrf_verify();
    $codeSaisi = trim($_POST['code'] ?? '');

    $stmt = $pdo->prepare('SELECT * FROM verifications_email WHERE email = ?');
    $stmt->execute([$email]);
    $pending = $stmt->fetch();

    if (!$pending) {
        redirect('register.php');
    }

    if (strtotime($pending['date_expiration']) < time()) {
        $errors[] = "Ce code a expiré. Merci d'en demander un nouveau.";
    } elseif ($pending['tentatives'] >= 5) {
        $errors[] = "Trop de tentatives incorrectes. Merci de redemander un nouveau code.";
    } elseif (!hash_equals($pending['code'], $codeSaisi)) {
        $pdo->prepare('UPDATE verifications_email SET tentatives = tentatives + 1 WHERE email = ?')->execute([$email]);
        $errors[] = "Code incorrect. Il vous reste " . (4 - $pending['tentatives']) . " tentative(s).";
    } else {
        // Code correct : on crée enfin le compte et l'école.
        $payload = json_decode($pending['payload'], true);

        $pdo->beginTransaction();
        try {
            $slug = slug_unique($pdo, $payload['nom_ecole']);
            $insEcole = $pdo->prepare("INSERT INTO ecoles (nom, slug, domaine, ville, statut) VALUES (?, ?, ?, ?, 'en_attente')");
            $insEcole->execute([$payload['nom_ecole'], $slug, $payload['domaine'], $payload['ville']]);
            $ecoleId = $pdo->lastInsertId();

            $insUser = $pdo->prepare("INSERT INTO utilisateurs (ecole_id, nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, 'admin_ecole')");
            $insUser->execute([$ecoleId, $payload['nom_admin'], $email, $payload['password_hash']]);

            $pdo->prepare('DELETE FROM verifications_email WHERE email = ?')->execute([$email]);

            $pdo->commit();

            attempt_login($email, ''); // ne fonctionnera pas car mot de passe déjà hashé : on connecte manuellement
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Une erreur est survenue lors de la création du compte. Merci de réessayer.";
        }

        if (empty($errors)) {
            // Connexion manuelle (on a déjà vérifié l'identité via le code)
            $stmtUser = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
            $stmtUser->execute([$email]);
            $user = $stmtUser->fetch();
            unset($user['mot_de_passe']);
            $_SESSION['user'] = $user;

            set_flash('success', 'Email vérifié ! Votre école a été enregistrée et sera visible après validation par notre équipe.');
            redirect('admin/dashboard.php');
        }
    }
}