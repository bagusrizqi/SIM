<?php
session_start();

// LANGSUNG CEK USERNAME SAJA — PASSWORD DIABAIKAN
$username = trim($_POST['username'] ?? '');

if ($username === '' || $username === null) {
    header("Location: ../login.php?error=1");
    exit;
}

// Kalau username "admin" (huruf kecil) → masuk admin
if (strtolower($username) === 'admin') {
    $_SESSION['login'] = true;
    $_SESSION['username'] = 'admin';
    $_SESSION['role'] = 'admin';
    header("Location: ../admin/dashboard.php");
    exit;
}

// Semua username lain → masuk user
$_SESSION['login'] = true;
$_SESSION['username'] = ucfirst($username); // biar kapital depan
$_SESSION['role'] = 'user';
header("Location: ../user/dashboard.php");
exit;
?>