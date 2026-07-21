<?php 
        $q = trim($_GET['q'] ?? '');

        $ville = trim($_GET['ville'] ?? '');
        $domaine = trim($_GET['domaine'] ?? '');

        $sql = "SELECT * FROM ecoles WHERE statut = 'valide'";
        $params = [];

        if ($q !== '') {
            $sql .= " AND (nom LIKE ? OR description LIKE ? OR domaine LIKE ?)";
            $like = '%' . $q . '%';
            $params[] = $like; $params[] = $like; $params[] = $like;
        }
        if ($ville !== '') {
            $sql .= " AND ville = ?";
            $params[] = $ville;
        }
        if ($domaine !== '') {
            $sql .= " AND domaine = ?";
            $params[] = $domaine;
        }
        $sql .= " ORDER BY (plan = 'premium') DESC, date_creation DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $resultats = $stmt->fetchAll();

        $villes = $pdo->query("SELECT DISTINCT ville FROM ecoles WHERE statut='valide' AND ville IS NOT NULL AND ville!=''")->fetchAll(PDO::FETCH_COLUMN);
        $domaines = $pdo->query("SELECT DISTINCT domaine FROM ecoles WHERE statut='valide' AND domaine IS NOT NULL AND domaine!=''")->fetchAll(PDO::FETCH_COLUMN);