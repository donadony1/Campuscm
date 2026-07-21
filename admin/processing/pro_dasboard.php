<?php

$ecoleId = current_user()['ecole_id'];

$stmt = $pdo->prepare('SELECT * FROM ecoles WHERE id = ?');
$stmt->execute([$ecoleId]);
$ecole = $stmt->fetch();

$nbFilieres = $pdo->prepare('SELECT COUNT(*) AS n FROM filieres WHERE ecole_id = ?');
$nbFilieres->execute([$ecoleId]);
$nbFilieres = $nbFilieres->fetch()['n'];

$nbPhotos = $pdo->prepare('SELECT COUNT(*) AS n FROM medias WHERE ecole_id = ?');
$nbPhotos->execute([$ecoleId]);
$nbPhotos = $nbPhotos->fetch()['n'];

$nbAvis = $pdo->prepare('SELECT COUNT(*) AS n, AVG(note) AS moy FROM avis WHERE ecole_id = ?');
$nbAvis->execute([$ecoleId]);
$avisData = $nbAvis->fetch();