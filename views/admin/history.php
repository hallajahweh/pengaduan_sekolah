<?php
session_start();
require_once __DIR__ . '/../../config/koneksi.php';

/* CEK LOGIN ADMIN */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

/* AMBIL DATA + JOIN SISWA */
$data = mysqli_query($conn, "
SELECT 
    aspirasi.*, 
    kategori.ket_kategori, 
    feedback.feedback,
    siswa.nama,
    siswa.kelas
FROM aspirasi
LEFT JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
LEFT JOIN feedback ON aspirasi.id_aspirasi = feedback.id_aspirasi
LEFT JOIN siswa ON aspirasi.nis = siswa.nis
ORDER BY aspirasi.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Histori Pengaduan</title>

<link rel="stylesheet" href="../../css/index_new.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #F4F7F6, #FDFDFD);
    min-height: 100vh;
    color: #1F2937;
}

.page-header {
    margin-top: 48px;
    margin-bottom: 24px;
}

.page-header h3 {
    font-size: 2rem;
    color: #0F172A;
}

.history-card {
    background: rgba(255,255,255,0.9);
    padding: 32px;
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.08);
}

.table-custom {
    width: 100%;
    border-collapse: collapse;
}

.table-custom th {
    background: #4A90E2;
    color: white;
    padding: 12px;
}

.table-custom td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

.badge-warning { background: orange; color: #fff; padding:5px 10px; border-radius:8px; }
.badge-primary { background: #4A90E2; color:#fff; padding:5px 10px; border-radius:8px; }
.badge-success { background: green; color:#fff; padding:5px 10px; border-radius:8px; }
</style>
</head>

<body>

<div class="container">

<div class="page-header">
    <h3>Histori Pengaduan (Admin)</h3>
</div>

<a href="dashboard.php" class="btn btn-secondary mb-3">
    ← Kembali
</a>

<div class="history-card">

<table class="table-custom">

<thead>
<tr>
<th>No</th>
<th>NIS</th>
<th>Nama</th> <!-- TAMBAHAN -->
<th>Kelas</th> <!-- TAMBAHAN -->
<th>Kategori</th>
<th>Laporan</th>
<th>Status</th>
<th>Feedback</th>
<th>Tanggal</th>
</tr>
</thead>

<tbody>

<?php 
if ($data && mysqli_num_rows($data) > 0) {
    $no = 1;
    while ($d = mysqli_fetch_assoc($data)) {

        $status = $d['status'];

        if ($status == "Menunggu") {
            $badge = "badge-warning";
        } elseif ($status == "Proses") {
            $badge = "badge-primary";
        } else {
            $badge = "badge-success";
        }
?>

<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($d['nis']) ?></td>

<!-- TAMBAHAN -->
<td><?= htmlspecialchars($d['nama'] ?? '-') ?></td>
<td><?= htmlspecialchars($d['kelas'] ?? '-') ?></td>

<td><?= htmlspecialchars($d['ket_kategori']) ?></td>
<td><?= htmlspecialchars($d['keterangan']) ?></td>

<td>
<span class="<?= $badge ?>">
<?= $status ?>
</span>
</td>

<td>
<?= $d['feedback'] ? htmlspecialchars($d['feedback']) : 'Belum ada' ?>
</td>

<td><?= htmlspecialchars($d['created_at']) ?></td>
</tr>

<?php 
    }
} else {
?>
<tr>
<td colspan="9" style="text-align:center;">Tidak ada data</td>
</tr>
<?php } ?>

</tbody>
</table>

</div>
</div>

</body>
</html>