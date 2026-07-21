<?php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Retourne l'utilisateur connecté (array) ou null. */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

/** Bloque l'accès si non connecté. */
function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

/** Bloque l'accès si le rôle ne correspond pas. */
function require_role(string $role): void
{
    require_login();
    if (current_user()['role'] !== $role) {
        http_response_code(403);
        die('Accès refusé : vous n\'avez pas les droits nécessaires.');
    }
}

/**
 * Tente de connecter un utilisateur. Retourne true/false.
 */
function attempt_login(string $email, string $password): bool
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        unset($user['mot_de_passe']);
        $_SESSION['user'] = $user;
        return true;
    }
    return false;
}

function logout_user(): void
{
    $_SESSION = [];
    session_destroy();
}
