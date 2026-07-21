<?php

$ecoleId = current_user()['ecole_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $filename = handle_image_upload('photo', UPLOAD_DIR_PHOTOS);
    if ($filename) {
        $ins = $pdo->prepare('INSERT INTO medias (ecole_id, chemin) VALUES (?, ?)');
        $ins->execute([$ecoleId, $filename]);
        set_flash('success', 'Photo ajoutée à la galerie.');
    } else {
        set_flash('error', 'Merci de sélectionner une image valide (jpg, png, webp - max 3 Mo).');
    }
    redirect('photos');
}

$stmt = $pdo->prepare('SELECT * FROM medias WHERE ecole_id = ? ORDER BY date_ajout DESC');
$stmt->execute([$ecoleId]);
$photos = $stmt->fetchAll();