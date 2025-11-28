<?php
// config.php

// Mulai session BISA ditaruh di sini, ATAU di file utama (dashboard/login)
// Karena Anda sudah memilikinya di sini, kita pertahankan.
session_start();

// Detail Koneksi Database XAMPP
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_rental"; // Pastikan ini adalah nama database Anda

// Koneksi ke database
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Kalau gagal konek
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Catatan: Variabel $conn sekarang tersedia untuk semua file yang menggunakan require 'config.php';

// TIDAK ADA kode penutup ?> 