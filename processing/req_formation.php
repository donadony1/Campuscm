<?php

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT f.*, e.nom AS ecole_nom, e.slug AS ecole_slug, e.plan AS ecole_plan, e.telephone AS ecole_telephone, e.ville AS ecole_ville
    FROM filieres f
    JOIN ecoles e ON e.id = f.ecole_id
    WHERE f.id = ? AND e.statut = 'valide'
");
$stmt->execute([$id]);
$formation = $stmt->fetch();

if (!$formation) {
    http_response_code(404);
    $pageTitle = 'Formation introuvable';
    require_once 'includes/header.php';
    echo '<div class="container py-5 text-center"><h1 class="fw-bold">Formation introuvable</h1><p class="text-muted">Cette formation n\'existe pas ou n\'est plus disponible.</p><a href="formations" class="btn btn-primary">Voir toutes les formations</a></div>';
    require_once 'includes/footer.php';
    exit;
}

// Autres formations de la même école (suggestions)
$autresStmt = $pdo->prepare("SELECT * FROM filieres WHERE ecole_id = ? AND id != ? ORDER BY nom LIMIT 4");
$autresStmt->execute([$formation['ecole_id'], $formation['id']]);
$autresFormations = $autresStmt->fetchAll();

// Suggestions aléatoires (autres écoles), en priorisant toujours le Premium
$formationsSuggerees = get_formations_suggerees($pdo, (int)$formation['id'], (int)$formation['ecole_id'], 4);
