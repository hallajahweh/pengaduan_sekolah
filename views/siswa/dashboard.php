<?php
session_start();
include __DIR__ . '/../../config/koneksi.php';

if (!isset($_SESSION['nis'])) {
    header("Location: /pengaduan_sekolah/views/auth/login.php");
    exit;
}

$nis = $_SESSION['nis'];

/* DATA SISWA */
$stmt = mysqli_prepare($conn, "SELECT nama, kelas FROM siswa WHERE nis=?");
mysqli_stmt_bind_param($stmt, "s", $nis);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($data);

$nama = $siswa['nama'] ?? 'Siswa';
$kelas = $siswa['kelas'] ?? '-';

/* HITUNG DATA */
function getCount($conn, $nis, $status=null){
    if($status){
        $stmt = mysqli_prepare($conn,"SELECT COUNT(*) FROM aspirasi WHERE nis=? AND status=?");
        mysqli_stmt_bind_param($stmt,"ss",$nis,$status);
    }else{
        $stmt = mysqli_prepare($conn,"SELECT COUNT(*) FROM aspirasi WHERE nis=?");
        mysqli_stmt_bind_param($stmt,"s",$nis);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_row($res)[0];
}

$total = getCount($conn,$nis);
$menunggu = getCount($conn,$nis,'Menunggu');
$proses = getCount($conn,$nis,'Proses');
$selesai = getCount($conn,$nis,'Selesai');
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Siswa</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
*{margin:0;padding:0;box-sizing:border-box;}

body{
    background:linear-gradient(135deg,#F4F7F6,#FDFDFD);
    font-family:'Poppins',sans-serif;
}

/* HEADER */
.header-school{
    background:linear-gradient(135deg,#4A90E2,#357ABD);
    color:white;
    text-align:center;
    padding:25px;
}

/* CONTAINER */
.container{
    max-width:1100px;
    margin:30px auto;
    padding:0 15px;
}

/* CARD */
.card{
    background:white;
    border-radius:15px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
    margin-bottom:20px;
    padding:20px;
}

/* HEADER CARD */
.card-header{
    background:#4A90E2;
    color:white;
    padding:10px;
    border-radius:10px;
    margin-bottom:15px;
}

/* WELCOME */
.welcome-card{
    background:linear-gradient(135deg,#4A90E2,#357ABD);
    color:white;
    text-align:center;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:15px;
}

/* STAT */
.stat-card{
    text-align:center;
    color:white;
    padding:20px;
    border-radius:15px;
}

.stat-card:nth-child(1){background:#4A90E2;}
.stat-card:nth-child(2){background:#f59e0b;}
.stat-card:nth-child(3){background:#ef4444;}
.stat-card:nth-child(4){background:#10b981;}

.stat-card h4{
    font-size:28px;
}

/* BUTTON */
.btn{
    padding:10px 15px;
    border-radius:10px;
    color:white;
    text-decoration:none;
    margin:5px;
    display:inline-block;
}

.btn-success{background:#10b981;}
.btn-primary{background:#4A90E2;}
.btn-danger{background:#ef4444;}

.btn:hover{opacity:0.8;}

/* GRAFIK */
.chart-box{
    height:220px;
}

/* FOOTER */
footer{
    text-align:center;
    padding:20px;
    background:#2C3E50;
    color:white;
}
</style>

</head>

<body>

<div class="header-school">
    <h1>Sistem Pengaduan Sekolah</h1>
    <p>Dashboard Siswa</p>
</div>

<div class="container">

    <!-- WELCOME -->
    <div class="card welcome-card">
        <h2>Dashboard Siswa</h2>
        <h3>Selamat datang, <?= htmlspecialchars($nama) ?>!</h3>
    </div>

    <!-- STAT -->
    <div class="grid">
        <div class="stat-card">
            <h4><?= $total ?></h4>
            <p>Total</p>
        </div>
        <div class="stat-card">
            <h4><?= $menunggu ?></h4>
            <p>Menunggu</p>
        </div>
        <div class="stat-card">
            <h4><?= $proses ?></h4>
            <p>Proses</p>
        </div>
        <div class="stat-card">
            <h4><?= $selesai ?></h4>
            <p>Selesai</p>
        </div>
    </div>

    <!-- GRAFIK -->
    <div class="card">
        <div class="card-header">
            Grafik Status Aspirasi
        </div>
        <div class="chart-box">
            <canvas id="chartStatus"></canvas>
        </div>
    </div>

    <!-- BUTTON -->
    <div class="card" style="text-align:center;">
        <a href="input_aspirasi.php" class="btn btn-success">+ Kirim Aspirasi</a>
        <a href="histori.php" class="btn btn-primary">Riwayat</a>
        <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- PROFIL -->
    <div class="card">
        <div class="card-header">Profil</div>
        <p><b>NIS:</b> <?= $nis ?></p>
        <p><b>Nama:</b> <?= htmlspecialchars($nama) ?></p>
        <p><b>Kelas:</b> <?= htmlspecialchars($kelas) ?></p>
    </div>

</div>

<footer>
    <p>&copy; 2026 Sistem Pengaduan Sekolah</p>
</footer>

<script>
new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
        labels: ['Menunggu','Proses','Selesai'],
        datasets: [{
            data: [<?= $menunggu ?>, <?= $proses ?>, <?= $selesai ?>],
            backgroundColor: ['#f59e0b','#4A90E2','#10b981']
        }]
    },
    options: {
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{position:'bottom'}
        }
    }
});
</script>

</body>
</html>