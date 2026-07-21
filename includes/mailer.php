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