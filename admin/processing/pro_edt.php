<?php 

$ecoleId = current_user()['ecole_id'];

$stmt = $pdo->prepare('SELECT * FROM ecoles WHERE id = ?');
$stmt->execute([$ecoleId]);
$ecole = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $nom = trim($_POST['nom'] ?? '');
    $domaine = trim($_POST['domaine'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $siteWeb = trim($_POST['site_web'] ?? '');

    if ($nom === '') {
        set_flash('error', "Le nom de l'école est requis.");
        redirect('edit-profil');
    }

    $slug = $ecole['slug'];
    if ($nom !== $ecole['nom']) {
        $slug = slug_unique($pdo, $nom, $ecoleId);
    }

    $logo = $ecole['logo'];
    $newLogo = handle_image_upload('logo', UPLOAD_DIR_LOGOS);
    if ($newLogo) $logo = $newLogo;

    $cover = $ecole['cover_image'];
    $newCover = handle_image_upload('cover_image', UPLOAD_DIR_COVERS);
    if ($newCover) $cover = $newCover;

    $upd = $pdo->prepare("UPDATE ecoles SET nom=?, slug=?, domaine=?, description=?, ville=?, adresse=?, telephone=?, email=?, site_web=?, logo=?, cover_image=? WHERE id=?");
    $upd->execute([$nom, $slug, $domaine, $description, $ville, $adresse, $telephone, $email, $siteWeb, $logo, $cover, $ecoleId]);

    set_flash('success', 'Profil mis à jour avec succès.');
    redirect('edit-profil');
}