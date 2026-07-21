-- ============================================================
-- CampusCM - Schéma MySQL / MariaDB pour hébergement mutualisé
-- À importer via phpMyAdmin (cPanel) dans une base vide.
-- Après import, pensez à mettre à jour includes/config.php :
--   DB_DRIVER = 'mysql', DB_HOST, DB_NAME, DB_USER, DB_PASS
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS ecoles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  domaine VARCHAR(150) NOT NULL DEFAULT 'Formation professionnelle',
  description TEXT,
  ville VARCHAR(120),
  adresse VARCHAR(255),
  telephone VARCHAR(50),
  email VARCHAR(150),
  site_web VARCHAR(255),
  logo VARCHAR(255),
  cover_image VARCHAR(255),
  statut ENUM('en_attente','valide','rejete') NOT NULL DEFAULT 'en_attente',
  plan ENUM('gratuit','premium') NOT NULL DEFAULT 'gratuit',
  premium_jusqu_au DATETIME NULL,
  vues INT NOT NULL DEFAULT 0,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS utilisateurs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ecole_id INT NULL,
  nom VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL,
  role ENUM('super_admin','admin_ecole') NOT NULL DEFAULT 'admin_ecole',
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS filieres (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ecole_id INT NOT NULL,
  nom VARCHAR(255) NOT NULL,
  niveau VARCHAR(120),
  duree VARCHAR(120),
  prix VARCHAR(120),
  description TEXT,
  FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS medias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ecole_id INT NOT NULL,
  chemin VARCHAR(255) NOT NULL,
  date_ajout DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS avis (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ecole_id INT NOT NULL,
  nom_visiteur VARCHAR(150) NOT NULL,
  note TINYINT NOT NULL,
  commentaire TEXT,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS verifications_email (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  code VARCHAR(10) NOT NULL,
  payload TEXT NOT NULL,
  tentatives INT NOT NULL DEFAULT 0,
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_expiration DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS paiements (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ecole_id INT NOT NULL,
  reference VARCHAR(150) NOT NULL UNIQUE,
  montant INT NOT NULL,
  devise VARCHAR(10) NOT NULL DEFAULT 'XAF',
  plan VARCHAR(20) NOT NULL DEFAULT 'premium',
  statut ENUM('en_attente','complete','echoue') NOT NULL DEFAULT 'en_attente',
  canal VARCHAR(50),
  date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_maj DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (ecole_id) REFERENCES ecoles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- Compte super-admin par défaut : admin@campuscm.cm / admin123
-- (hash généré avec password_hash('admin123', PASSWORD_DEFAULT))
INSERT INTO utilisateurs (ecole_id, nom, email, mot_de_passe, role)
VALUES (NULL, 'Super Admin', 'admin@campuscm.cm', '$2y$10$7Q2u111LOfNbpw/wUetUMOEl1chKzw9oTU4Rc5/fNPuoByFcMHHXK', 'super_admin');
-- ⚠️ CHANGEZ CE MOT DE PASSE IMMÉDIATEMENT APRÈS LE PREMIER DÉPLOIEMENT.
