<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . "/../../config/koneksi.php";

/* CEK LOGIN */
if(!isset($_SESSION['role']) || $_SESSION['role']!="admin"){
    header("Location: ../../index.php");
    exit;
}

/* UPDATE */
if(isset($_POST['update'])){
    $id = mysqli_real_escape_string($conn, $_POST['id_aspirasi']);
    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $cek = mysqli_query($conn, "SELECT * FROM feedback WHERE id_aspirasi='$id'");
    if(mysqli_num_rows($cek) > 0){
        mysqli_query($conn, "UPDATE feedback SET feedback='$feedback' WHERE id_aspirasi='$id'");
    } else {
        mysqli_query($conn, "INSERT INTO feedback(id_aspirasi, feedback) VALUES('$id', '$feedback')");
    }

    mysqli_query($conn, "UPDATE aspirasi SET status='$status' WHERE id_aspirasi='$id'");
    header('Location: feedback.php');
    exit;
}

/* HAPUS */
if(isset($_POST['hapus'])){
    $id = mysqli_real_escape_string($conn, $_POST['id_aspirasi']);
    mysqli_query($conn, "DELETE FROM feedback WHERE id_aspirasi='$id'");
    header('Location: feedback.php');
    exit;
}

/* DATA */
$data = mysqli_query($conn,"
SELECT aspirasi.*, feedback.feedback, siswa.nama, siswa.kelas
FROM aspirasi
LEFT JOIN feedback ON aspirasi.id_aspirasi = feedback.id_aspirasi
LEFT JOIN siswa ON aspirasi.nis = siswa.nis
ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Feedback Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #eef2ff, #f7fbff);
    color: #1f2937;
}
.navbar {
    background: #fff;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
}
.container {
    max-width: 1200px;
    margin: auto;
}
.card-custom {
    background: white;
    padding: 20px;
    border-radius: 15px;
}
.table th {
    background: linear-gradient(135deg, #22c55e, #0ea5e9);
    color: white;
    text-align: center;
}
</style>
</head>

<body>
<div class="container mt-4">

<h3>📊 Manajemen Feedback</h3>

<a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali</a>

<div class="card-custom">

<table class="table table-bordered table-hover">

<tr>
<th>No</th>
<th>NIS</th>
<th>Nama</th>
<th>Kelas</th>
<th>Aspirasi</th>
<th>Feedback</th>
<th>Status</th>
<th>Tanggal</th>
<th>Aksi</th>
</tr>

<?php $no=1; while($d=mysqli_fetch_assoc($data)){ ?>

<tr>
<form method="POST">

<td><?= $no++ ?></td>

<td><?= $d['nis'] ?></td>
<td><?= $d['nama'] ?></td>
<td><?= $d['kelas'] ?></td>

<td><?= $d['keterangan'] ?></td>

<td>
<textarea name="feedback" class="form-control"><?= $d['feedback'] ?></textarea>
</td>

<td>
<select name="status" class="form-control mb-1">
<option value="Menunggu" <?= $d['status']=="Menunggu"?'selected':'' ?>>Menunggu</option>
<option value="Proses" <?= $d['status']=="Proses"?'selected':'' ?>>Proses</option>
<option value="Selesai" <?= $d['status']=="Selesai"?'selected':'' ?>>Selesai</option>
</select>

<?php
if($d['status']=="Menunggu"){
echo "<span class='badge bg-warning text-dark'>⏳ Menunggu</span>";
}elseif($d['status']=="Proses"){
echo "<span class='badge bg-primary'>🔧 Proses</span>";
}else{
echo "<span class='badge bg-success'>✔ Selesai</span>";
}
?>
</td>

<td><?= $d['created_at'] ?></td>

<td>
<input type="hidden" name="id_aspirasi" value="<?= $d['id_aspirasi'] ?>">

<button name="update" class="btn btn-success btn-sm mb-1">💾</button>
<button name="hapus" class="btn btn-danger btn-sm" onclick="return confirm('Hapus?')">🗑</button>
</td>

</form>
</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>