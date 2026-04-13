<?php
session_start();
require_once __DIR__ . "/../../config/koneksi.php";

/* CEK LOGIN */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

/* AMBIL DATA SESSION */
$email = $_SESSION['email'] ?? 'admin';

function fetchCount($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return isset($row['j']) ? (int) $row['j'] : 0;
    }
    return 0;
}

$total_aspirasi = fetchCount($conn, "SELECT COUNT(*) as j FROM aspirasi");
$total_feedback = fetchCount($conn, "SELECT COUNT(*) as j FROM feedback");

$status_query = "SELECT 
        COUNT(CASE WHEN status='Selesai' THEN 1 END) as selesai,
        COUNT(CASE WHEN status='Proses' THEN 1 END) as proses,
        COUNT(CASE WHEN status='Menunggu' THEN 1 END) as menunggu
    FROM aspirasi";
$status_stats = mysqli_fetch_assoc(mysqli_query($conn, $status_query));

$selesai = isset($status_stats['selesai']) ? (int) $status_stats['selesai'] : 0;
$proses = isset($status_stats['proses']) ? (int) $status_stats['proses'] : 0;
$menunggu = isset($status_stats['menunggu']) ? (int) $status_stats['menunggu'] : 0;

// Data trend aspirasi 7 hari terakhir
$trend_labels = [];
$trend_data = [
    'menunggu' => array_fill(0, 7, 0),
    'proses' => array_fill(0, 7, 0),
    'selesai' => array_fill(0, 7, 0),
];
for ($i = 6; $i >= 0; $i--) {
    $trend_labels[] = date('d M', strtotime("-$i days"));
}

$trend_query = mysqli_query($conn, "SELECT DATE(created_at) as tanggal,
        SUM(CASE WHEN status='Menunggu' THEN 1 ELSE 0 END) as menunggu,
        SUM(CASE WHEN status='Proses' THEN 1 ELSE 0 END) as proses,
        SUM(CASE WHEN status='Selesai' THEN 1 ELSE 0 END) as selesai
    FROM aspirasi
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at)");

if ($trend_query) {
    while ($row = mysqli_fetch_assoc($trend_query)) {
        $index = array_search(date('d M', strtotime($row['tanggal'])), $trend_labels);
        if ($index !== false) {
            $trend_data['menunggu'][$index] = (int) $row['menunggu'];
            $trend_data['proses'][$index] = (int) $row['proses'];
            $trend_data['selesai'][$index] = (int) $row['selesai'];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard Admin</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #eef2ff, #f7fbff);
    color: #1f2937;
    margin: 0;
}

body::before {
    content: '';
    position: fixed;
    inset: 0;
    background: radial-gradient(circle at top left, rgba(59,130,246,0.18), transparent 28%),
                radial-gradient(circle at bottom right, rgba(16,185,129,0.15), transparent 24%);
    pointer-events: none;
}

/* NAVBAR */
.navbar {
    background: rgba(255, 255, 255, 0.88);
    padding: 18px 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
    backdrop-filter: blur(14px);
    position: sticky;
    top: 0;
    z-index: 10;
}

.navbar b {
    font-size: 1.1rem;
    color: #0f172a;
}

.menu a {
    margin-left: 12px;
    text-decoration: none;
    color: #334155;
    padding: 10px 14px;
    border-radius: 999px;
    transition: transform 0.25s ease, background 0.25s ease, color 0.25s ease;
}

.menu a:hover,
.menu a.active {
    background: rgba(56, 189, 248, 0.15);
    color: #0284c7;
    transform: translateY(-1px);
}

/* CONTAINER */
.container {
    padding: 24px 28px 40px;
    max-width: 1200px;
    margin: 0 auto;
}

/* WELCOME */
.welcome {
    background: linear-gradient(135deg, #ffffff, #f8fafc);
    padding: 24px;
    border-radius: 24px;
    box-shadow: 0 18px 30px rgba(15, 23, 42, 0.06);
    margin-bottom: 24px;
}

.welcome h3 {
    color: #0f172a;
    margin-bottom: 8px;
}

.welcome p {
    color: #475569;
    line-height: 1.7;
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

/* CARD */
.card {
    background: linear-gradient(145deg, #ffffff, #eff6ff);
    padding: 24px;
    border-radius: 22px;
    box-shadow: 0 16px 30px rgba(15, 23, 42, 0.05);
    text-align: center;
    border: 1px solid rgba(148, 163, 184, 0.15);
}

.card:hover {
    transform: translateY(-3px);
}

.icon {
    font-size: 24px;
    color: #ffffff;
    background: linear-gradient(135deg, #22c55e, #0ea5e9);
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    border-radius: 18px;
}

.card .number {
    font-size: 2rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 8px;
}

.card div {
    color: #475569;
    font-size: 0.95rem;
}

/* PROFILE */
.profile {
    background: linear-gradient(135deg, #ffffff, #f1f5f9);
    padding: 24px;
    border-radius: 22px;
    box-shadow: 0 16px 30px rgba(15, 23, 42, 0.05);
    margin-top: 20px;
}

.profile h4 {
    color: #0f172a;
    margin-bottom: 12px;
}

.profile p {
    color: #475569;
    line-height: 1.8;
}

.profile b {
    color: #0f172a;
}

/* CHART */
.charts {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 22px;
    margin-top: 24px;
}

.chart-box {
    background: linear-gradient(135deg, #ffffff, #f8fafc);
    padding: 24px;
    border-radius: 22px;
    box-shadow: 0 16px 30px rgba(15, 23, 42, 0.05);
    border: 1px solid rgba(148, 163, 184, 0.12);
}

.chart-box h4 {
    color: #0f172a;
    margin-bottom: 18px;
    letter-spacing: 0.02em;
}

.chart-wrapper {
    width: 100%;
    min-height: 340px;
}

.footer-note {
    text-align: center;
    color: #64748b;
    font-size: 0.95rem;
    margin-top: 28px;
}

@media (max-width: 900px) {
    .charts {
        grid-template-columns: 1fr;
    }
}
</style>

</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
<div><b>Admin Panel</b></div>
<div class="menu">
<a href="#">Dashboard</a>
<a href="feedback.php">Feedback</a>
<a href="history.php">History</a>
<a href="../auth/logout.php">Logout</a>
</div>
</div>

<div class="container">

<!-- WELCOME -->
<div class="welcome">
<h3>👨‍💼 Dashboard Admin</h3>
<p>Selamat datang, <b><?= $email ?></b>!</p>
</div>

<!-- STAT -->
<div class="grid">

<div class="card">
<div class="icon"><i class="fa fa-file"></i></div>
<div class="number"><?= $total_aspirasi ?></div>
<div>Total Aspirasi</div>
</div>

<div class="card">
<div class="icon"><i class="fa fa-star"></i></div>
<div class="number"><?= $total_feedback ?></div>
<div>Total Feedback</div>
</div>

</div>

<!-- PROFILE -->
<div class="profile">
<h4>👤 Profil Admin</h4>
<p><b>Username:</b> <?= $email ?></p>
<p><b>Role:</b> Admin</p>
</div>

<!-- CHART -->
<div class="charts">
    <div class="chart-box">
        <h4>Distribusi Status Pengaduan</h4>
        <div class="chart-wrapper">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
    <div class="chart-box">
        <h4>Trend Pengaduan 7 Hari Terakhir</h4>
        <div class="chart-wrapper">
            <canvas id="trendChart"></canvas>
        </div>
    </div>
</div>

<div class="footer-note">
    Data di atas menunjukkan distribusi aspirasi saat ini dan perkembangan pengaduan selama seminggu terakhir.
</div>

</div>

<script>
const statusCanvas = document.getElementById('statusChart');
const trendCanvas = document.getElementById('trendChart');

new Chart(statusCanvas, {
    type: 'doughnut',
    data: {
        labels: ['Selesai', 'Proses', 'Menunggu'],
        datasets: [{
            data: [<?= $selesai ?>, <?= $proses ?>, <?= $menunggu ?>],
            backgroundColor: ['#4caf50', '#42a5f5', '#ffb300'],
            borderColor: ['#ffffff', '#ffffff', '#ffffff'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 14 } }
        }
    }
});

const trendLabels = [<?php echo implode(',', array_map(function($label){ return "'" . $label . "'"; }, $trend_labels)); ?>];
const trendMenunggu = [<?php echo implode(',', $trend_data['menunggu']); ?>];
const trendProses = [<?php echo implode(',', $trend_data['proses']); ?>];
const trendSelesai = [<?php echo implode(',', $trend_data['selesai']); ?>];

new Chart(trendCanvas, {
    type: 'line',
    data: {
        labels: trendLabels,
        datasets: [
            {
                label: 'Menunggu',
                data: trendMenunggu,
                fill: true,
                backgroundColor: 'rgba(251, 191, 36, 0.18)',
                borderColor: '#f59e0b',
                borderWidth: 3,
                tension: 0.38,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#f59e0b',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
            },
            {
                label: 'Proses',
                data: trendProses,
                fill: true,
                backgroundColor: 'rgba(56, 189, 248, 0.18)',
                borderColor: '#38bdf8',
                borderWidth: 3,
                tension: 0.38,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#38bdf8',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
            },
            {
                label: 'Selesai',
                data: trendSelesai,
                fill: true,
                backgroundColor: 'rgba(34, 197, 94, 0.18)',
                borderColor: '#22c55e',
                borderWidth: 3,
                tension: 0.38,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointBackgroundColor: '#22c55e',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 14 } },
            tooltip: {
                mode: 'index',
                intersect: false,
                backgroundColor: 'rgba(15, 23, 42, 0.92)',
                titleColor: '#ffffff',
                bodyColor: '#e2e8f0'
            }
        },
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { color: '#64748b' }
            },
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1, color: '#64748b' },
                grid: { color: 'rgba(15, 23, 42, 0.06)' }
            }
        }
    }
});
</script>

</body>
</html>