<?php
session_start();
require '../config.php'; // pastikan config.php ada di root

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Method not allowed";
    exit;
}

// Ambil semua data dari form
$mobil          = $_POST['mobil'] ?? '';
$durasi         = $_POST['durasi'] ?? 0;
$total          = $_POST['total'] ?? 0;
$nama           = $_POST['nama_pelanggan'] ?? '';
$no_wa          = $_POST['no_wa'] ?? '';
$tanggal_sewa   = $_POST['tanggal_sewa'] ?? '';
$harga_per_hari = $_POST['harga_per_hari'] ?? 0;
$username       = $_SESSION['username'] ?? 'user';

$bukti = "";
if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === 0) {
    $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
    $nama_file = "bukti_" . time() . "_" . rand(1000,9999) . "." . $ext;
    $target = "../upload/bukti/" . $nama_file;
    
    if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target)) {
        $bukti = $nama_file;
    }
}

// Simpan ke database dengan semua kolom yang kamu punya
$sql = "INSERT INTO pesanan 
        (nama_pelanggan, no_wa, mobil, harga, tanggal_sewa, durasi_hari, total_bayar, bukti_transfer, username_user, status) 
        VALUES 
        ('$nama', '$no_wa', '$mobil', '$harga_per_hari', '$tanggal_sewa', '$durasi', '$total', '$bukti', '$username', 'pending')";

if (mysqli_query($conn, $sql)) {
    echo "success";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>