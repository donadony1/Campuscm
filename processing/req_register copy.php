<?php

if (is_logged_in()) {
    redirect(current_user()['role'] === 'super_admin' ? 'superadmin/dashboard.php' : 'admin/dashboard.php');
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
        $pdo->beginTransaction();
        try {
            $slug = slug_unique($pdo, $nomEcole);
            $insEcole = $pdo->prepare("INSERT INTO ecoles (nom, slug, domaine, ville, statut) VALUES (?, ?, ?, ?, 'en_attente')");
            $insEcole->execute([$nomEcole, $slug, $domaine ?: 'Formation professionnelle', $ville]);
            $ecoleId = $pdo->lastInsertId();

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insUser = $pdo->prepare("INSERT INTO utilisateurs (ecole_id, nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, 'admin_ecole')");
            $insUser->execute([$ecoleId, $nomAdmin, $email, $hash]);

            $pdo->commit();

            attempt_login($email, $password);
            set_flash('success', 'Votre école a été enregistrée ! Elle sera visible publiquement après validation par notre équipe. Vous pouvez déjà compléter votre profil.');
            redirect('admin/dashboard');
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Une erreur est survenue. Merci de réessayer.";
        }
    }
}