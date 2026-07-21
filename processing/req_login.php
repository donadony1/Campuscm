<?php

if (is_logged_in()) {
    redirect(current_user()['role'] === 'super_admin' ? 'superadmin/dashboard.php' : 'admin/dashboard');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (attempt_login($email, $password)) {
        $role = current_user()['role'];
        redirect($role === 'super_admin' ? 'superadmin/dashboard.php' : 'admin/dashboard');
    } else {
        $error = 'Email ou mot de passe incorrect.';
    }
}