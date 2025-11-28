<?php
session_start();
// Pastikan require 'config.php' ada di sini jika Anda akan menggunakannya, 
// tapi untuk dashboard user saja, config tidak wajib jika hanya menampilkan. 
// Namun, file 'proses_booking.php' pasti memerlukannya.
// if (file_exists('../config.php')) {
//     require '../config.php';
// }

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Rental-diGue</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        /* CSS yang sudah ada */
        body { background:#f4f6f9; font-family:Arial,sans-serif; padding:20px; }
        .container { max-width:1200px; margin:auto; }
        .header { background:#4361ee; color:white; padding:30px; border-radius:15px; text-align:center; margin-bottom:30px; }
        .armada-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:25px; }
        .card { background:white; border-radius:15px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.1); }
        .card-img img { width:100%; height:200px; object-fit:cover; }
        .card-body { padding:20px; text-align:center; }
        .card-body h3 { margin:0 0 10px; font-size:20px; }
        .price { font-size:22px; font-weight:bold; color:#e74c3c; margin:15px 0; }
        .form-booking { margin:20px 0; }
        .form-booking input { width:100%; padding:12px; margin:8px 0; border:1px solid #ddd; border-radius:8px; font-size:16px; }
        .total { font-size:24px; font-weight:bold; color:#25d366; margin:15px 0; }
        .btn-book { background:#25d366; color:white; padding:14px; border:none; border-radius:50px; font-size:18px; font-weight:bold; cursor:pointer; width:100%; }
        .btn-book:hover { background:#1da851; }
        .logout { position:fixed; top:20px; right:20px; background:#e74c3c; color:white; padding:12px 25px; border-radius:50px; text-decoration:none; font-weight:bold; }

        /* HALAMAN PEMBAYARAN QRIS (CSS asli Anda) */
        #pembayaranPopup { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:9999; justify-content:center; align-items:center; }
        .popup-box { background:white; width:70%; max-width:500px; border-radius:20px; padding:40px; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.4); }
        .popup-box img { max-width:180px; margin:20px 0; border:1px solid #ddd; border-radius:12px; }
        .upload-area { border:2px dashed #ddd; border-radius:12px; padding:30px; margin:20px 0; cursor:pointer; transition:0.3s; }
        .upload-area:hover { border-color:#25d366; background:#f8fff8; }
        .btn-selesai { background:#25d366; color:white; padding:16px 40px; border:none; border-radius:50px; font-size:18px; font-weight:bold; cursor:pointer; }
        
        /* CSS TAMBAHAN UNTUK FORM DETAIL */
        #detailBookingPopup { 
            display:none; 
            position:fixed; 
            top:0; 
            left:0; 
            width:100%; 
            height:100%; 
            background:rgba(0,0,0,0.9); 
            z-index:9998; 
            justify-content:center; 
            align-items:center; 
        }
    </style>
</head>
<body>

<a href="../logout.php" class="logout">LOGOUT</a>

<div class="container">
    <div class="header">
        <h1>Halo, <?= htmlspecialchars($username) ?>!</h1>
        <p>Pilih mobil dan tentukan berapa hari kamu mau sewa</p>
    </div>

    <div class="armada-grid">
        <div class="card">
            <div class="card-img"><img src="../img/zenix.png" alt="Innova Zenix"></div>
            <div class="card-body">
                <h3>INNOVA ZENIX</h3>
                <p class="price">Rp 900.000 / hari</p>
                <div class="form-booking">
                    <input type="number" min="1" placeholder="Berapa hari?" oninput="hitungTotal(this, 900000)">
                    <div class="total">Total: Rp 0</div>
                    <button class="btn-book" onclick="bookingSekarang('Innova Zenix', 900000, this)">BOOKING SEKARANG</button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-img"><img src="../img/innova.png" alt="Innova Reborn"></div>
            <div class="card-body">
                <h3>INNOVA REBORN</h3>
                <p class="price">Rp 700.000 / hari</p>
                <div class="form-booking">
                    <input type="number" min="1" placeholder="Berapa hari?" oninput="hitungTotal(this, 700000)">
                    <div class="total">Total: Rp 0</div>
                    <button class="btn-book" onclick="bookingSekarang('Innova Reborn', 700000, this)">BOOKING SEKARANG</button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-img"><img src="../img/avanza.jpg" alt="Avanza"></div>
            <div class="card-body">
                <h3>GRAND NEW AVANZA</h3>
                <p class="price">Rp 500.000 / hari</p>
                <div class="form-booking">
                    <input type="number" min="1" placeholder="Berapa hari?" oninput="hitungTotal(this, 500000)">
                    <div class="total">Total: Rp 0</div>
                    <button class="btn-book" onclick="bookingSekarang('Grand New Avanza', 500000, this)">BOOKING SEKARANG</button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-img"><img src="../img/xpander.png" alt="Xpander"></div>
            <div class="card-body">
                <h3>MITSUBISHI XPANDER</h3>
                <p class="price">Rp 550.000 / hari</p>
                <div class="form-booking">
                    <input type="number" min="1" placeholder="Berapa hari?" oninput="hitungTotal(this, 550000)">
                    <div class="total">Total: Rp 0</div>
                    <button class="btn-book" onclick="bookingSekarang('Mitsubishi Xpander', 550000, this)">BOOKING SEKARANG</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="detailBookingPopup">
    <div class="popup-box">
        <h2>LENGKAPI DETAIL BOOKING</h2>
        <form id="formDetailBooking" onsubmit="return false;">
            <input type="hidden" id="inputMobil" name="mobil">
            <input type="hidden" id="inputHarga" name="harga">
            <input type="hidden" id="inputHari" name="durasi_hari">
            <input type="hidden" id="inputTotal" name="total_bayar">

            <input type="text" id="inputNama" name="nama_pelanggan" placeholder="Nama Lengkap" required>
            <input type="tel" id="inputNoWa" name="no_wa" placeholder="Nomor WhatsApp (Aktif)" required>
            <input type="date" id="inputTanggalSewa" name="tanggal_sewa" placeholder="Tanggal Mulai Sewa" required>
            
            <p id="infoBookingDetail" style="margin:15px 0; font-weight:bold;"></p>
            <button type="button" class="btn-book" onclick="lanjutkanPembayaran()">LANJUTKAN PEMBAYARAN</button>
        </form>
    </div>
</div>
<div id="pembayaranPopup">
    <div class="popup-box">
        <h2>PEMBAYARAN QRIS</h2>
        <p id="infoBooking"></p>
        <img src="../img/qris.jpeg" alt="QRIS Pembayaran">
        <p>Silakan transfer sesuai jumlah di atas</p>

        <div class="upload-area" onclick="document.getElementById('buktiTF').click()">
            <p><strong>Upload Bukti Transfer</strong><br>Klik di sini untuk upload</p>
            <input type="file" id="buktiTF" accept="image/*" style="display:none;" onchange="tampilkanNamaFile(this)">
            <p id="namaFile" style="margin-top:10px;color:#666;">Belum ada file dipilih</p>
        </div>

        <button class="btn-selesai" onclick="selesaiBooking()">SELESAI & KIRIM BUKTI</button>
    </div>
</div>

<script>
let bookingData = {};

function hitungTotal(input, hargaHarian) {
    const hari = parseInt(input.value) || 0;
    const total = hari * hargaHarian;
    // Format Rupiah (Indonesia)
    const formattedTotal = total.toLocaleString('id-ID'); 
    input.parentElement.querySelector('.total').innerText = "Total: Rp " + formattedTotal;
}

function bookingSekarang(mobil, harga, button) {
    const inputHari = button.parentElement.querySelector('input[type="number"]');
    const hari = parseInt(inputHari.value) || 0;
    
    if (hari < 1) {
        alert("Masukkan jumlah hari dulu bro!");
        return;
    }
    
    const total = hari * harga;

    // 1. Simpan data dasar ke objek global
    bookingData = { mobil, harga, hari, total };

    // 2. Tampilkan data di form detail
    document.getElementById("infoBookingDetail").innerHTML = 
        `Mobil: <strong>${mobil}</strong> (${hari} hari)<br>
        Total Bayar: <strong style="color:#25d366;">Rp ${total.toLocaleString('id-ID')}</strong>`;

    // 3. Isi hidden input untuk form pengiriman data ke server
    document.getElementById("inputMobil").value = mobil;
    document.getElementById("inputHarga").value = harga;
    document.getElementById("inputHari").value = hari;
    document.getElementById("inputTotal").value = total;

    // 4. Tampilkan form detail booking
    document.getElementById("detailBookingPopup").style.display = "flex";
}


function lanjutkanPembayaran() {
    const form = document.getElementById('formDetailBooking');
    if (!form.reportValidity()) { // Cek validasi form HTML5 (required)
        return;
    }

    // Ambil data dari input form detail
    bookingData.nama_pelanggan = document.getElementById('inputNama').value;
    bookingData.no_wa = document.getElementById('inputNoWa').value;
    bookingData.tanggal_sewa = document.getElementById('inputTanggalSewa').value;
    
    // Sembunyikan form detail
    document.getElementById("detailBookingPopup").style.display = "none";
    
    // Tampilkan popup QRIS
    document.getElementById("infoBooking").innerHTML = 
        `<strong>${bookingData.mobil}</strong><br>
        ${bookingData.hari} hari × Rp ${bookingData.harga.toLocaleString('id-ID')} = <strong style="color:#25d366;">Rp ${bookingData.total.toLocaleString('id-ID')}</strong>`;
        
    document.getElementById("pembayaranPopup").style.display = "flex";
}


function tampilkanNamaFile(input) {
    if (input.files && input.files[0]) {
        document.getElementById("namaFile").innerText = input.files[0].name;
        // Tambahkan file bukti transfer ke objek bookingData
        bookingData.bukti_transfer = input.files[0];
    }
}


function selesaiBooking() {
  const formData = new FormData();
  
  // Data dari booking
  formData.append('mobil', bookingData.mobil);
  formData.append('durasi', bookingData.hari);
  formData.append('total', bookingData.total);
  formData.append('harga_per_hari', bookingData.harga);
  
  // Data dari form detail
  formData.append('nama_pelanggan', document.getElementById('inputNama').value);
  formData.append('no_wa', document.getElementById('inputNoWa').value);
  formData.append('tanggal_sewa', document.getElementById('inputTanggalSewa').value);

  // Bukti transfer
  const fileInput = document.getElementById('buktiTF');
  if (!fileInput.files[0]) {
    alert("Upload bukti transfer dulu bro!");
    return;
  }
  formData.append('bukti', fileInput.files[0]);

  fetch('../proses/simpan_booking.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.text())
  .then(result => {
    if (result.trim() === "success") {
      alert("Booking berhasil! Semua data sudah masuk ke admin.\nKami akan segera konfirmasi via WhatsApp.");
      document.getElementById("pembayaranPopup").style.display = "none";
      location.reload();
    } else {
      alert("Gagal: " + result);
    }
  })
  .catch(err => {
    alert("Error koneksi: " + err);
  });
}
</script>
</body>
</html>