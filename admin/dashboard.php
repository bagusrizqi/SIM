<?php 
require '../config.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// HAPUS PESANAN (HANYA KALAU SUDAH LUNAS)
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status, bukti_transfer FROM pesanan WHERE id='$id'"));
    if ($cek && $cek['status'] !== 'lunas') {
        echo "<script>alert('Hanya pesanan yang sudah LUNAS yang bisa dihapus!'); window.location='dashboard.php';</script>";
        exit;
    }
    if ($cek && $cek['bukti_transfer'] && file_exists("../upload/bukti/".$cek['bukti_transfer'])) {
        unlink("../upload/bukti/".$cek['bukti_transfer']);
    }
    mysqli_query($conn, "DELETE FROM pesanan WHERE id='$id'");
    header("Location: dashboard.php");
    exit;
}

// KONFIRMASI LUNAS
if (isset($_GET['konfirmasi'])) {
    $id = intval($_GET['konfirmasi']);
    mysqli_query($conn, "UPDATE pesanan SET status='lunas' WHERE id='$id'");
    header("Location: dashboard.php");
    exit;
}

// Hitung statistik
$total_pemasukan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_bayar),0) as total FROM pesanan WHERE status='lunas'"))['total'];
$bulan_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_bayar),0) as total FROM pesanan WHERE status='lunas' AND MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())"))['total'];
$pending = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM pesanan WHERE status='pending'"));
$lunas_hari_ini = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM pesanan WHERE status='lunas' AND DATE(created_at)=CURDATE()"));

// GRAFIK 6 BULAN — 100% SINKRON DENGAN DATA TABEL
$grafik_data = [];
for ($i = 5; $i >= 0; $i--) {
    $bulan = date('Y-m', strtotime("-$i month"));
    $nama_bulan = date('M Y', strtotime("-$i month"));
    
    // PAKE tanggal_sewa BIAR SINKRON DENGAN BOOKING USER!
    $q = mysqli_query($conn, "SELECT COALESCE(SUM(total_bayar),0) as total FROM pesanan WHERE status='lunas' AND DATE_FORMAT(tanggal_sewa,'%Y-%m')='$bulan'");
    $row = mysqli_fetch_assoc($q);
    
    $grafik_data[] = [
        'bulan' => $nama_bulan,
        'total' => (int)$row['total']
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Rental-diGue</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: 'Poppins', sans-serif; background:#f0f2f5; margin:0; padding:20px; }
    .container { max-width:1400px; margin:auto; }
    .header { background:linear-gradient(135deg,#25d366,#1da851); color:white; padding:30px; border-radius:20px; text-align:center; margin-bottom:30px; }
    .stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:20px; margin-bottom:40px; }
    .card { background:white; padding:25px; border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,0.1); text-align:center; }
    .card h3 { margin:0 0 15px; color:#666; }
    .jumlah { font-size:36px; font-weight:800; color:#25d366; }
    .chart-container { background:white; padding:30px; border-radius:15px; box-shadow:0 10px 30px rgba(0,0,0,0.1); margin:30px 0; }
    table { width:100%; border-collapse:collapse; background:white; border-radius:15px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.1); margin-top:20px; }
    th { background:#25d366; color:white; padding:18px; }
    td { padding:15px; text-align:center; border-bottom:1px solid #eee; }
    .lunas { color:#25d366; font-weight:bold; }
    .pending { color:#f39c12; font-weight:bold; }
    .logout { position:fixed; top:20px; right:20px; background:#e74c3c; color:white; padding:12px 25px; border-radius:50px; text-decoration:none; font-weight:bold; z-index:100; }

    .btn-konfirmasi { background:#27ae60; color:white; padding:8px 16px; border-radius:8px; text-decoration:none; font-size:14px; margin:0 5px; }
    .btn-konfirmasi:hover { background:#219a52; }
    .btn-hapus { background:#e74c3c; color:white; padding:8px 16px; border-radius:8px; text-decoration:none; font-size:14px; margin:0 5px; }
    .btn-hapus:hover { background:#c0392b; }
    .btn-lihat { color:#3498db; text-decoration:underline; font-size:14px; }
  </style>
</head>
<body>

<a href="../logout.php" class="logout">LOGOUT</a>

<div class="container">
  <div class="header">
    <h1>ADMIN DASHBOARD - RENTAL-DIGUE</h1>
    <p>Selamat datang kembali, <?= $_SESSION['username'] ?? 'Admin' ?>!</p>
  </div>

  <div class="stats">
    <div class="card">
      <h3>Total Pemasukan</h3>
      <div class="jumlah">Rp <?=number_format($total_pemasukan)?></div>
    </div>
    <div class="card">
      <h3>Pemasukan Bulan Ini</h3>
      <div class="jumlah">Rp <?=number_format($bulan_ini)?></div>
    </div>
    <div class="card">
      <h3>Pesanan Pending</h3>
      <div class="jumlah"><?=$pending?> order</div>
    </div>
    <div class="card">
      <h3>Lunas Hari Ini</h3>
      <div class="jumlah"><?=$lunas_hari_ini?> order</div>
    </div>
  </div>

  <!-- GRAFIK -->
  <div class="chart-container">
    <h2 style="text-align:center;margin-bottom:30px;color:#333;">Grafik Pemasukan 6 Bulan Terakhir</h2>
    <canvas id="grafikPemasukan" height="100"></canvas>
  </div>

  <!-- TABEL PESANAN -->
  <h2 style="text-align:center;margin:40px 0 20px;color:#333;">Riwayat Booking dari User</h2>
  <table>
    <tr>
      <th>No</th>
      <th>Pelanggan</th>
      <th>No WA</th>
      <th>Mobil</th>
      <th>Harga/Hari</th>
      <th>Tanggal Sewa</th>
      <th>Durasi</th>
      <th>Total Bayar</th>
      <th>Bukti TF</th>
      <th>Status</th>
      <th>Aksi</th>
    </tr>
    <?php 
    $no = 1;
    $q = mysqli_query($conn, "SELECT * FROM pesanan ORDER BY created_at DESC");
    while($d = mysqli_fetch_assoc($q)): ?>
    <tr>
      <td><?=$no++?></td>
      <td><?=$d['nama_pelanggan']?></td>
      <td><?=$d['no_wa']?></td>
      <td><?=$d['mobil']?></td>
      <td>Rp <?=number_format($d['harga'])?></td>
      <td><?=date('d/m/Y', strtotime($d['tanggal_sewa']))?></td>
      <td><?=$d['durasi_hari']?> hari</td>
      <td>Rp <?=number_format($d['total_bayar'])?></td>
      <td>
        <?php if($d['bukti_transfer'] && file_exists("../upload/bukti/".$d['bukti_transfer'])): ?>
          <a href="../upload/bukti/<?=$d['bukti_transfer']?>" target="_blank" class="btn-lihat">Lihat</a>
        <?php else: ?>
          -
        <?php endif; ?>
      </td>
      <td class="<?= $d['status']=='lunas' ? 'lunas' : 'pending' ?>">
        <?=ucfirst($d['status'])?>
      </td>
      <td>
        <?php if($d['status']=='pending'): ?>
          <a href="?konfirmasi=<?=$d['id']?>" class="btn-konfirmasi" 
             onclick="return confirm('Yakin konfirmasi LUNAS?')">Konfirmasi</a>
        <?php endif; ?>
        <?php if($d['status']=='lunas'): ?>
          <a href="?hapus=<?=$d['id']?>" class="btn-hapus" 
             onclick="return confirm('Yakin HAPUS pesanan ini?')">Hapus</a>
        <?php endif; ?>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<script>
const dataGrafik = <?= json_encode($grafik_data) ?>;
new Chart(document.getElementById('grafikPemasukan'), {
  type: 'bar',
  data: {
    labels: dataGrafik.map(x => x.bulan),
    datasets: [{
      label: 'Pemasukan',
      data: dataGrafik.map(x => x.total),
      backgroundColor: '#25d366',
      borderColor: '#1da851',
      borderWidth: 2,
      borderRadius: 8
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: { 
      y: { 
        beginAtZero: true, 
        ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') } 
      } 
    }
  }
});
</script>
</body>
</html>