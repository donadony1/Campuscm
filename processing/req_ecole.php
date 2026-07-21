<?php 

$slug = trim($_GET['slug'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM ecoles WHERE slug = ? AND statut = 'valide'");
$stmt->execute([$slug]);
$ecole = $stmt->fetch();

if (!$ecole) {
    http_response_code(404);
    echo '<div class="container py-5 text-center"><h1 class="fw-bold">École introuvable</h1><p class="text-muted">Cette vitrine n\'existe pas ou n\'est pas encore validée.</p><a href="recherche.php" class="btn btn-primary">Retour à la recherche</a></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Incrémente le compteur de vues (best-effort)
$pdo->prepare("UPDATE ecoles SET vues = vues + 1 WHERE id = ?")->execute([$ecole['id']]);

$pageTitle = $ecole['nom'];

$filieres = $pdo->prepare("SELECT * FROM filieres WHERE ecole_id = ? ORDER BY nom");
$filieres->execute([$ecole['id']]);
$filieres = $filieres->fetchAll();

$photos = $pdo->prepare("SELECT * FROM medias WHERE ecole_id = ? ORDER BY date_ajout DESC");
$photos->execute([$ecole['id']]);
$photos = $photos->fetchAll();

$avis = $pdo->prepare("SELECT * FROM avis WHERE ecole_id = ? ORDER BY date_creation DESC");
$avis->execute([$ecole['id']]);
$avis = $avis->fetchAll();
$noteMoyenne = count($avis) ? array_sum(array_column($avis, 'note')) / count($avis) : 0;

// Traitement soumission d'un avis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_avis'])) {
    csrf_verify();
    $nomVisiteur = trim($_POST['nom_visiteur'] ?? '');
    $note = max(1, min(5, (int)($_POST['note'] ?? 5)));
    $commentaire = trim($_POST['commentaire'] ?? '');
    if ($nomVisiteur !== '' && $commentaire !== '') {
        $ins = $pdo->prepare("INSERT INTO avis (ecole_id, nom_visiteur, note, commentaire) VALUES (?, ?, ?, ?)");
        $ins->execute([$ecole['id'], $nomVisiteur, $note, $commentaire]);
        set_flash('success', 'Merci pour votre avis !');
        redirect('ecole?slug=' . urlencode($slug) . '#avis');
    } else {
        set_flash('error', 'Merci de remplir votre nom et votre commentaire.');
        redirect('ecole?slug=' . urlencode($slug) . '#avis');
    }
}