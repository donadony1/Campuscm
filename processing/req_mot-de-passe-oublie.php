<?php

if (is_logged_in()) {
    redirect(current_user()['role'] === 'super_admin' ? 'superadmin/dashboard.php' : 'admin/dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Merci de saisir une adresse email valide.';
    } else {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // On ne révèle jamais si un email existe ou non dans la base (protection
        // contre l'énumération de comptes) : on redirige toujours vers la même
        // page suivante, qui affiche un message générique. L'email n'est
        // réellement envoyé que si le compte existe.
        if ($user) {
            $secondesEcoulees = null;
            $existing = $pdo->prepare('SELECT date_creation FROM reinitialisations_mdp WHERE email = ?');
            $existing->execute([$email]);
            $row = $existing->fetch();
            if ($row) {
                $secondesEcoulees = time() - strtotime($row['date_creation']);
            }

            if ($secondesEcoulees === null || $secondesEcoulees >= PASSWORD_RESET_COOLDOWN_SECONDES) {
                $code = generate_verification_code();
                $maintenant = date('Y-m-d H:i:s');
                $expiration = date('Y-m-d H:i:s', time() + PASSWORD_RESET_DUREE_MINUTES * 60);

                $upsert = $pdo->prepare("
                    INSERT INTO reinitialisations_mdp (email, code, tentatives, date_creation, date_expiration)
                    VALUES (?, ?, 0, ?, ?)
                    ON CONFLICT(email) DO UPDATE SET
                        code = excluded.code,
                        tentatives = 0,
                        date_creation = excluded.date_creation,
                        date_expiration = excluded.date_expiration
                ");
                $upsert->execute([$email, $code, $maintenant, $expiration]);

                send_password_reset_email($email, $user['nom'], $code);
            }
            // Si le cooldown n'est pas écoulé, on ne renvoie pas de nouveau code,
            // mais on affiche quand même le même message générique (pas de fuite d'info).
        }

        redirect('reinitialiser-mot-de-passe?email=' . urlencode($email));
    }
}