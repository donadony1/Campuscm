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
        redirect('filieres.php');
    }

    if ($filiereId > 0) {
        // Vérifie que la filière appartient bien à cette école avant modif
        $check = $pdo->prepare('SELECT ecole_id FROM filieres WHERE id = ?');
        $check->execute([$filiereId]);
        $owner = $check->fetch();
        if (!$owner || (int)$owner['ecole_id'] !== (int)$ecoleId) {
            set_flash('error', 'Action non autorisée.');
            redirect('filieres.php');
        }
        $upd = $pdo->prepare('UPDATE filieres SET nom=?, niveau=?, duree=?, prix=?, description=? WHERE id=?');
        $upd->execute([$nom, $niveau, $duree, $prix, $description, $filiereId]);
        set_flash('success', 'Filière mise à jour.');
    } else {
        $ins = $pdo->prepare('INSERT INTO filieres (ecole_id, nom, niveau, duree, prix, description) VALUES (?, ?, ?, ?, ?, ?)');
        $ins->execute([$ecoleId, $nom, $niveau, $duree, $prix, $description]);
        set_flash('success', 'Filière ajoutée.');
    }
    redirect('filieres.php');
}

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $del = $pdo->prepare('DELETE FROM filieres WHERE id = ? AND ecole_id = ?');
    $del->execute([$id, $ecoleId]);
    set_flash('success', 'Filière supprimée.');
    redirect('filieres.php');
}

$stmt = $pdo->prepare('SELECT * FROM filieres WHERE ecole_id = ? ORDER BY nom');
$stmt->execute([$ecoleId]);
$filieres = $stmt->fetchAll();