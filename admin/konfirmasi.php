<?php
require '../config.php';
session_start();

// Cek admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Cek ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard_admin.php");
    exit;
}

$id_pesanan = mysqli_real_escape_string($conn, $_GET['id']);

$query = "UPDATE pesanan SET status='lunas' WHERE id='$id_pesanan'";

if (mysqli_query($conn, $query)) {
    header("Location: dashboard_admin.php?status=sukses");
} else {
    header("Location: dashboard_admin.php?status=gagal");
}
exit;
?>