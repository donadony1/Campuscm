<?php
require_once __DIR__ . '/config.php';

/**
 * Retourne une instance PDO unique (singleton simple via variable statique).
 */
function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    try {
        if (DB_DRIVER === 'sqlite') {
            $pdo = new PDO('sqlite:' . SQLITE_PATH);
            $pdo->exec('PRAGMA foreign_keys = ON;');
        } else {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
        }
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die('Erreur de connexion à la base de données : ' . ($e->getMessage()));
    }

    // En SQLite (dev/démo), on s'assure que les tables existent.
    if (DB_DRIVER === 'sqlite') {
        install_sqlite_schema($pdo);
    }

    return $pdo;
}

/**
 * Crée les tables SQLite si elles n'existent pas encore, et insère
 * un compte super-admin par défaut au tout premier lancement.
 */
function install_sqlite_schema(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS ecoles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        domaine TEXT NOT NULL DEFAULT 'Formation professionnelle',
        description TEXT,
        ville TEXT,
        adresse TEXT,
        telephone TEXT,
        email TEXT,
        site_web TEXT,
        logo TEXT,
        cover_image TEXT,
        statut TEXT NOT NULL DEFAULT 'en_attente', -- en_attente | valide | rejete
        plan TEXT NOT NULL DEFAULT 'gratuit', -- gratuit | premium
        premium_jusqu_au TEXT,
        vues INTEGER NOT NULL DEFAULT 0,
        date_creation TEXT NOT NULL DEFAULT (datetime('now'))
    )");

    // Migration douce pour les bases déjà créées avant l'ajout du plan premium
    try { $pdo->exec("ALTER TABLE ecoles ADD COLUMN plan TEXT NOT NULL DEFAULT 'gratuit'"); } catch (Exception $e) {}
    try { $pdo->exec("ALTER TABLE ecoles ADD COLUMN premium_jusqu_au TEXT"); } catch (Exception $e) {}

    $pdo->exec("CREATE TABLE IF NOT EXISTS utilisateurs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ecole_id INTEGER,
        nom TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        mot_de_passe TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'admin_ecole', -- super_admin | admin_ecole
        date_creation TEXT NOT NULL DEFAULT (datetime('now')),
        FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS filieres (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ecole_id INTEGER NOT NULL,
        nom TEXT NOT NULL,
        niveau TEXT,
        duree TEXT,
        prix TEXT,
        description TEXT,
        FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS medias (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ecole_id INTEGER NOT NULL,
        chemin TEXT NOT NULL,
        date_ajout TEXT NOT NULL DEFAULT (datetime('now')),
        FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS avis (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ecole_id INTEGER NOT NULL,
        nom_visiteur TEXT NOT NULL,
        note INTEGER NOT NULL,
        commentaire TEXT,
        date_creation TEXT NOT NULL DEFAULT (datetime('now')),
        FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS paiements (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ecole_id INTEGER NOT NULL,
        reference TEXT NOT NULL UNIQUE,
        montant INTEGER NOT NULL,
        devise TEXT NOT NULL DEFAULT 'XAF',
        plan TEXT NOT NULL DEFAULT 'premium',
        statut TEXT NOT NULL DEFAULT 'en_attente', -- en_attente | complete | echoue
        canal TEXT,
        date_creation TEXT NOT NULL DEFAULT (datetime('now')),
        date_maj TEXT NOT NULL DEFAULT (datetime('now')),
        FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE
    )");

    // ajouter la table verifications_email pour gérer les codes de vérification d'email
    $pdo->exec("CREATE TABLE IF NOT EXISTS verifications_email (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT NOT NULL UNIQUE,
        code TEXT NOT NULL,
        payload TEXT NOT NULL,
        tentatives INTEGER NOT NULL DEFAULT 0,
        date_creation TEXT NOT NULL DEFAULT (datetime('now')),
        date_expiration TEXT NOT NULL
    )");

    // Compte super-admin par défaut (créé une seule fois)
    $stmt = $pdo->query("SELECT COUNT(*) AS n FROM utilisateurs WHERE role = 'super_admin'");
    if ((int)$stmt->fetch()['n'] === 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO utilisateurs (ecole_id, nom, email, mot_de_passe, role) VALUES (NULL, 'Super Admin', 'admin@campuscm.cm', ?, 'super_admin')");
        $ins->execute([$hash]);
    }
}
