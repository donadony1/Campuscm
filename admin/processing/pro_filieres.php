<?php
$ecoleId = current_user()['ecole_id'];

// Ajout ou modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $nom = trim($_POST['nom'] ?? '');
    $niveau = trim($_POST['niveau'] ?? '');
    $duree = trim($_POST['duree'] ?? '');
    $prix = trim($_POST['prix'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $filiereId = (int)($_POST['filiere_id'] ?? 0);

    if ($nom === '') {
        set_flash('error', 'Le nom de la filière est requis.');
        redirect('filieres');
    }

    $nouvelleImage = handle_image_upload('image', UPLOAD_DIR_FORMATIONS);

    if ($filiereId > 0) {
        // Vérifie que la filière appartient bien à cette école avant modif
        $check = $pdo->prepare('SELECT * FROM filieres WHERE id = ?');
        $check->execute([$filiereId]);
        $existante = $check->fetch();
        if (!$existante || (int)$existante['ecole_id'] !== (int)$ecoleId) {
            set_flash('error', 'Action non autorisée.');
            redirect('filieres');
        }

        // On garde l'image actuelle si aucune nouvelle n'a été envoyée
        $image = $nouvelleImage ?: $existante['image'];
        if ($nouvelleImage && $existante['image']) {
            $ancienFichier = UPLOAD_DIR_FORMATIONS . $existante['image'];
            if (file_exists($ancienFichier)) {
                @unlink($ancienFichier);
            }
        }

        $upd = $pdo->prepare('UPDATE filieres SET nom=?, niveau=?, duree=?, prix=?, description=?, image=? WHERE id=?');
        $upd->execute([$nom, $niveau, $duree, $prix, $description, $image, $filiereId]);
        set_flash('success', 'Filière mise à jour.');
    } else {
        $ins = $pdo->prepare('INSERT INTO filieres (ecole_id, nom, niveau, duree, prix, description, image) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $ins->execute([$ecoleId, $nom, $niveau, $duree, $prix, $description, $nouvelleImage]);
        set_flash('success', 'Filière ajoutée.');
    }
    redirect('filieres');
}

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Récupère l'image avant suppression pour nettoyer le fichier sur le disque
    $stmtImg = $pdo->prepare('SELECT image FROM filieres WHERE id = ? AND ecole_id = ?');
    $stmtImg->execute([$id, $ecoleId]);
    $image = $stmtImg->fetchColumn();

    $del = $pdo->prepare('DELETE FROM filieres WHERE id = ? AND ecole_id = ?');
    $del->execute([$id, $ecoleId]);

    if ($image) {
        $fichier = UPLOAD_DIR_FORMATIONS . $image;
        if (file_exists($fichier)) {
            @unlink($fichier);
        }
    }

    set_flash('success', 'Filière supprimée.');
    redirect('filieres');
}

$stmt = $pdo->prepare('SELECT * FROM filieres WHERE ecole_id = ? ORDER BY nom');
$stmt->execute([$ecoleId]);
$filieres = $stmt->fetchAll();
