<?php

// On rétrograde d'abord les écoles dont le premium a expiré (portable MySQL/SQLite)
downgrade_expired_premiums($pdo);

$q = trim($_GET['q'] ?? '');
$ville = trim($_GET['ville'] ?? '');
$domaine = trim($_GET['domaine'] ?? '');

$sql = "
    SELECT f.*, e.nom AS ecole_nom, e.slug AS ecole_slug, e.plan AS ecole_plan, e.ville AS ecole_ville, e.domaine AS ecole_domaine
    FROM filieres f
    JOIN ecoles e ON e.id = f.ecole_id
    WHERE e.statut = 'valide'
";
$params = [];

if ($q !== '') {
    $sql .= " AND (f.nom LIKE ? OR f.description LIKE ? OR e.nom LIKE ?)";
    $like = '%' . $q . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($ville !== '') {
    $sql .= " AND e.ville = ?";
    $params[] = $ville;
}
if ($domaine !== '') {
    $sql .= " AND e.domaine = ?";
    $params[] = $domaine;
}

// Les formations des écoles Premium sont toujours mises en avant en premier
$sql .= " ORDER BY (e.plan = 'premium') DESC, f.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$formations = $stmt->fetchAll();

$villes = $pdo->query("SELECT DISTINCT ville FROM ecoles WHERE statut='valide' AND ville IS NOT NULL AND ville!=''")->fetchAll(PDO::FETCH_COLUMN);
$domaines = $pdo->query("SELECT DISTINCT domaine FROM ecoles WHERE statut='valide' AND domaine IS NOT NULL AND domaine!=''")->fetchAll(PDO::FETCH_COLUMN);
