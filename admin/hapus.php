<?php
session_start();
require '../config.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard_admin.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Hapus bukti transfer kalau ada
$result = mysqli_query($conn, "SELECT bukti_transfer FROM pesanan WHERE id='$id'");
if ($result && $row = mysqli_fetch_assoc($result)) {
    if (!empty($row['bukti_transfer'])) {
        $file = "../upload/bukti/" . $row['bukti_transfer'];
        if (file_exists($file)) unlink($file);
    }
}

// Hapus dari database
mysqli_query($conn, "DELETE FROM pesanan WHERE id='$id'");

header("Location: dashboard_admin.php?status=hapus_sukses");
exit;
?>