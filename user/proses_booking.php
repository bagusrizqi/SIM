<?php
// Tampilkan semua error untuk debugging (Hapus/comment setelah berhasil)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan path ke config.php benar (asumsi di root proyek: "../config.php")
require '../admin/config.php'; 
session_start();

// Mengirimkan header JSON agar frontend bisa memproses respons dengan fetch
header('Content-Type: application/json');

// --- Pengecekan Keamanan Awal ---
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit;
}

// 1. Ambil data dari POST
$nama_pelanggan = $_POST['nama_pelanggan'] ?? '';
$no_wa          = $_POST['no_wa'] ?? '';
$mobil          = $_POST['mobil'] ?? '';
$harga_satuan   = $_POST['harga'] ?? 0;
$tanggal_sewa   = $_POST['tanggal_sewa'] ?? '';
$durasi_hari    = $_POST['durasi_hari'] ?? 0;
$total_bayar    = $_POST['total_bayar'] ?? 0;
$username       = $_POST['username'] ?? '';

// Cek data formulir yang wajib
if (empty($nama_pelanggan) || empty($mobil) || $durasi_hari < 1) {
    echo json_encode(['success' => false, 'message' => 'Data formulir tidak lengkap atau durasi sewa salah.']);
    exit;
}


// --- 2. Proses Upload Bukti Transfer ---
// KOREKSI PATH: Menggunakan "../upload/bukti/" sesuai struktur folder Anda
$target_dir = "../upload/bukti"; 
$bukti_nama = '';
$target_file = ''; // Inisialisasi variabel untuk unlink jika query gagal

// Buat folder jika belum ada dan pastikan izinnya (hanya untuk jaga-jaga)
if (!is_dir($target_dir)) {
    if (!mkdir($target_dir, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Gagal membuat folder upload. Cek izin folder manual.']);
        exit;
    }
}

if (isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] === UPLOAD_ERR_OK) {
    $file_tmp  = $_FILES['bukti_transfer']['tmp_name'];
    
    // Amankan nama file dengan uniqid
    $file_ext  = strtolower(pathinfo($_FILES['bukti_transfer']['name'], PATHINFO_EXTENSION));
    $bukti_nama = uniqid('bukti_'). '.' . $file_ext;
    $target_file = $target_dir . $bukti_nama;
    
    if (!move_uploaded_file($file_tmp, $target_file)) {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupload bukti transfer. Cek izin folder ' . $target_dir]);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Bukti transfer tidak ditemukan atau upload gagal.']);
    exit;
}

// --- 3. Masukkan Data ke Database ---
global $conn; 
$status = 'pending'; 
$created_at = date('Y-m-d H:i:s');

$stmt = $conn->prepare("INSERT INTO pesanan (nama_pelanggan, no_wa, mobil, harga, durasi_hari, tanggal_sewa, total_bayar, status, bukti_transfer, created_at, username_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Gunakan "s" untuk string, "i" untuk integer, "d" untuk double/decimal (sesuaikan tipe DB Anda)
// Pastikan jumlah 's', 'i', 'd' adalah 11!
$stmt->bind_param("sssiisdssss", 
    $nama_pelanggan, 
    $no_wa, 
    $mobil, 
    $harga_satuan, 
    $durasi_hari, 
    $tanggal_sewa, 
    $total_bayar, 
    $status, 
    $bukti_nama, 
    $created_at, 
    $username
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Pesanan berhasil dicatat.']);
} else {
    // Jika gagal, hapus file yang sudah terlanjur diupload
    // Hanya hapus jika file berhasil diupload ($target_file tidak kosong)
    if (!empty($target_file) && file_exists($target_file)) {
         @unlink($target_file); 
    }
    
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data ke database: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>