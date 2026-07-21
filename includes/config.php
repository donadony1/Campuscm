<?php
/**
 * Configuration principale de l'application
 * ----------------------------------------------------------
 * Pour la démo / le développement local, on utilise SQLite (aucune
 * installation de serveur MySQL requise, un simple fichier .sqlite).
 *
 * Pour la MISE EN PRODUCTION sur un hébergement mutualisé (cPanel, LWS,
 * Hostinger, etc.), il suffit de :
 *   1. Changer DB_DRIVER à 'mysql'
 *   2. Renseigner les identifiants MySQL fournis par ton hébergeur
 *   3. Importer database/schema_mysql.sql via phpMyAdmin
 * ----------------------------------------------------------
 */

// ================= DRIVER BASE DE DONNÉES =================
define('DB_DRIVER', 'sqlite'); // 'sqlite' ou 'mysql'

// --- Paramètres SQLite (dev/démo) ---
define('SQLITE_PATH', __DIR__ . '/../data/campuscm.sqlite');

// --- Paramètres MySQL (production) ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'campuscm');
define('DB_USER', 'root');
define('DB_PASS', '');

// ================= PARAMÈTRES GÉNÉRAUX =================
define('APP_NAME', 'CampusCM');
define('APP_URL', 'http://localhost:8000'); // à changer en production (ex: https://campuscm.cm)

// Dossiers d'upload (chemins absolus + chemins relatifs pour les <img src="">)
define('UPLOAD_DIR_LOGOS', __DIR__ . '/../assets/uploads/logos/');
define('UPLOAD_DIR_COVERS', __DIR__ . '/../assets/uploads/covers/');
define('UPLOAD_DIR_PHOTOS', __DIR__ . '/../assets/uploads/photos/');

define('UPLOAD_URL_LOGOS', 'assets/uploads/logos/');
define('UPLOAD_URL_COVERS', 'assets/uploads/covers/');
define('UPLOAD_URL_PHOTOS', 'assets/uploads/photos/');

define('MAX_UPLOAD_SIZE', 3 * 1024 * 1024); // 3 Mo
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// ================= NOTCH PAY (paiement Mobile Money) =================
// Clés à récupérer sur https://business.notchpay.co (Développeurs > Clés API)
// Utilisez les clés "sandbox" (sb.xxx) pour tester, puis les clés "live" (b.xxx) en prod.
define('NOTCHPAY_PUBLIC_KEY', 'sb.votre_cle_publique_sandbox');
define('NOTCHPAY_WEBHOOK_HASH', 'votre_hash_webhook'); // "Hash Key" du webhook, pour vérifier les signatures // "Hash Key" du webhook, pour vérifier les signatures
define('NOTCHPAY_API_BASE', 'https://api.notchpay.co');

// Tarifs de la plateforme (en FCFA / XAF)
define('PLAN_PREMIUM_PRIX_MENSUEL', 5000);
define('PLAN_PREMIUM_DUREE_JOURS', 30);

// ================= ENVOI D'EMAILS (vérification de compte) =================
define('MAIL_DRIVER', 'mail'); // 'mail' (fonction PHP native) ou 'smtp' (recommandé en prod)
define('MAIL_FROM_EMAIL', 'no-reply@campuscm.cm');
define('MAIL_FROM_NAME', 'CampusCM');

// Paramètres SMTP (utilisés seulement si MAIL_DRIVER = 'smtp')
// Beaucoup d'hébergeurs bloquent mail() ou l'envoient en spam : le SMTP est plus fiable.
define('SMTP_HOST', 'smtp.votrehebergeur.cm');
define('SMTP_PORT', 587);
define('SMTP_USER', 'no-reply@campuscm.cm');
define('SMTP_PASS', '');
define('SMTP_SECURE', 'tls'); // 'tls' ou 'ssl'

define('VERIFICATION_CODE_DUREE_MINUTES', 15);
define('VERIFICATION_RESEND_COOLDOWN_SECONDES', 60);

// Affichage des erreurs (à mettre à false en production)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

date_default_timezone_set('Africa/Douala');
