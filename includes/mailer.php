<?php
/**
 * Envoi d'emails sans dépendance externe (Composer).
 * - Driver 'mail' : utilise la fonction PHP native mail() (simple, mais
 *   souvent filtré en spam sur certains hébergeurs).
 * - Driver 'smtp' : client SMTP minimal en socket brut (EHLO, STARTTLS,
 *   AUTH LOGIN, DATA), sans librairie externe. Recommandé en production.
 */

require_once __DIR__ . '/config.php';

function mail_send(string $to, string $subject, string $htmlBody): bool
{
    if (MAIL_DRIVER === 'smtp') {
        return smtp_send($to, $subject, $htmlBody);
    }
    return mail_send_native($to, $subject, $htmlBody);
}

function mail_send_native(string $to, string $subject, string $htmlBody): bool
{
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM_EMAIL . '>';

    return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $htmlBody, implode("\r\n", $headers));
}

/**
 * Client SMTP minimal (sans Composer). Supporte STARTTLS + AUTH LOGIN.
 * Suffisant pour Gmail SMTP, la plupart des hébergeurs mutualisés,
 * et les fournisseurs comme Brevo (ex-Sendinblue), Mailgun, etc.
 */
function smtp_send(string $to, string $subject, string $htmlBody): bool
{
    $errno = 0;
    $errstr = '';
    $timeout = 15;

    $host = (SMTP_SECURE === 'ssl') ? 'ssl://' . SMTP_HOST : SMTP_HOST;
    $socket = @fsockopen($host, SMTP_PORT, $errno, $errstr, $timeout);
    if (!$socket) {
        error_log("SMTP: connexion impossible ($errstr)");
        return false;
    }

    $read = function () use ($socket) {
        $data = '';
        while ($line = fgets($socket, 515)) {
            $data .= $line;
            if (substr($line, 3, 1) === ' ') break; // fin de la réponse multi-lignes
        }
        return $data;
    };

    $write = function (string $cmd) use ($socket) {
        fwrite($socket, $cmd . "\r\n");
    };

    $read(); // bannière du serveur

    $write('EHLO campuscm.cm');
    $read();

    if (SMTP_SECURE === 'tls') {
        $write('STARTTLS');
        $read();
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($socket);
            return false;
        }
        $write('EHLO campuscm.cm');
        $read();
    }

    if (!empty(SMTP_USER)) {
        $write('AUTH LOGIN');
        $read();
        $write(base64_encode(SMTP_USER));
        $read();
        $write(base64_encode(SMTP_PASS));
        $authResp = $read();
        if (strpos($authResp, '235') !== 0 && strpos($authResp, '235') === false) {
            error_log('SMTP: authentification échouée');
            fclose($socket);
            return false;
        }
    }

    $write('MAIL FROM:<' . MAIL_FROM_EMAIL . '>');
    $read();
    $write('RCPT TO:<' . $to . '>');
    $read();
    $write('DATA');
    $read();

    $headers = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">\r\n";
    $headers .= "To: <$to>\r\n";
    $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $body = str_replace("\n.", "\n..", $htmlBody); // échappe les lignes commençant par un point

    $write($headers . "\r\n" . $body . "\r\n.");
    $sendResp = $read();

    $write('QUIT');
    fclose($socket);

    return (strpos($sendResp, '250') === 0);
}

/** Génère un code de vérification à 6 chiffres */
function generate_verification_code(): string
{
    return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/** Envoie l'email contenant le code de vérification */
function send_verification_email(string $to, string $nomAdmin, string $code): bool
{
    $subject = 'Votre code de vérification CampusCM';
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 480px; margin: auto;'>
      <h2 style='color:#0d6efd;'>Vérification de votre compte</h2>
      <p>Bonjour " . htmlspecialchars($nomAdmin) . ",</p>
      <p>Voici votre code de vérification pour finaliser l'inscription de votre école sur CampusCM :</p>
      <p style='font-size: 28px; font-weight: bold; letter-spacing: 6px; background:#f1f3f5; padding: 12px 20px; border-radius: 8px; text-align:center;'>$code</p>
      <p>Ce code expire dans " . VERIFICATION_CODE_DUREE_MINUTES . " minutes.</p>
      <p style='color:#888; font-size: 13px;'>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</p>
    </div>";

    return mail_send($to, $subject, $body);
}

/** Envoie l'email contenant le code de réinitialisation de mot de passe */
function send_password_reset_email(string $to, string $nomAdmin, string $code): bool
{
    $subject = 'Réinitialisation de votre mot de passe CampusCM';
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 480px; margin: auto;'>
      <h2 style='color:#0d6efd;'>Réinitialisation de mot de passe</h2>
      <p>Bonjour " . htmlspecialchars($nomAdmin) . ",</p>
      <p>Voici votre code pour réinitialiser votre mot de passe CampusCM :</p>
      <p style='font-size: 28px; font-weight: bold; letter-spacing: 6px; background:#f1f3f5; padding: 12px 20px; border-radius: 8px; text-align:center;'>$code</p>
      <p>Ce code expire dans " . PASSWORD_RESET_DUREE_MINUTES . " minutes.</p>
      <p style='color:#888; font-size: 13px;'>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email : votre mot de passe actuel reste valide.</p>
    </div>";

    return mail_send($to, $subject, $body);
}

/**
 * Notifie tous les super-admins de la plateforme qu'une nouvelle école
 * vient de s'inscrire et attend une validation. Envoyée juste après la
 * création réelle du compte (après vérification de l'email par l'école).
 */
function notify_superadmins_new_school(PDO $pdo, string $ecoleNom, string $ville, string $domaine, string $adminNom, string $adminEmail): void
{
    $stmt = $pdo->query("SELECT email FROM utilisateurs WHERE role = 'super_admin'");
    $superadmins = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($superadmins)) {
        return;
    }

    $subject = 'Nouvelle école inscrite sur CampusCM : ' . $ecoleNom;
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 480px; margin: auto;'>
      <h2 style='color:#0d6efd;'>Nouvelle école en attente de validation</h2>
      <p>Une nouvelle école vient de s'inscrire sur CampusCM et attend votre validation :</p>
      <table style='width:100%; border-collapse: collapse; margin: 16px 0;'>
        <tr><td style='padding:6px 0; color:#666;'>École</td><td style='padding:6px 0; font-weight:bold;'>" . htmlspecialchars($ecoleNom) . "</td></tr>
        <tr><td style='padding:6px 0; color:#666;'>Ville</td><td style='padding:6px 0;'>" . htmlspecialchars($ville ?: 'Non renseignée') . "</td></tr>
        <tr><td style='padding:6px 0; color:#666;'>Domaine</td><td style='padding:6px 0;'>" . htmlspecialchars($domaine) . "</td></tr>
        <tr><td style='padding:6px 0; color:#666;'>Responsable</td><td style='padding:6px 0;'>" . htmlspecialchars($adminNom) . "</td></tr>
        <tr><td style='padding:6px 0; color:#666;'>Email</td><td style='padding:6px 0;'>" . htmlspecialchars($adminEmail) . "</td></tr>
      </table>
      <p><a href='" . e(rtrim(APP_URL, '/')) . "/superadmin/ecoles.php?statut=en_attente' style='display:inline-block; background:#0d6efd; color:#fff; padding:10px 20px; border-radius:6px; text-decoration:none;'>Voir et valider</a></p>
    </div>";

    foreach ($superadmins as $email) {
        mail_send($email, $subject, $body);
    }
}

/**
 * Notifie le responsable d'une école que sa fiche a été validée ou rejetée
 * par un super-admin.
 */
function send_school_status_email(string $to, string $nomAdmin, string $ecoleNom, string $statut): bool
{
    if ($statut === 'valide') {
        $subject = 'Votre école est maintenant en ligne sur CampusCM';
        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 480px; margin: auto;'>
          <h2 style='color:#198754;'>Bonne nouvelle !</h2>
          <p>Bonjour " . htmlspecialchars($nomAdmin) . ",</p>
          <p>La fiche de <strong>" . htmlspecialchars($ecoleNom) . "</strong> a été validée par notre équipe et est désormais visible publiquement sur CampusCM.</p>
          <p><a href='" . e(rtrim(APP_URL, '/')) . "/admin/dashboard' style='display:inline-block; background:#198754; color:#fff; padding:10px 20px; border-radius:6px; text-decoration:none;'>Voir mon dashboard</a></p>
        </div>";
    } elseif ($statut === 'rejete') {
        $subject = 'Votre fiche CampusCM nécessite des corrections';
        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 480px; margin: auto;'>
          <h2 style='color:#dc3545;'>Votre fiche n'a pas été validée</h2>
          <p>Bonjour " . htmlspecialchars($nomAdmin) . ",</p>
          <p>La fiche de <strong>" . htmlspecialchars($ecoleNom) . "</strong> n'a pas été validée par notre équipe. Merci de vérifier que les informations fournies sont complètes et exactes, puis de nous contacter si besoin.</p>
          <p><a href='" . e(rtrim(APP_URL, '/')) . "/admin/edit-profil' style='display:inline-block; background:#0d6efd; color:#fff; padding:10px 20px; border-radius:6px; text-decoration:none;'>Compléter mon profil</a></p>
        </div>";
    } else {
        return false;
    }

    return mail_send($to, $subject, $body);
}