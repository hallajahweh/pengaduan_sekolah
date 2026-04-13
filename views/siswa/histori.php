<?php
// Mulai session dan cek login siswa
session_start();
include __DIR__ . '/../../config/koneksi.php';

if (!isset($_SESSION['nis'])) {
    header("Location: /pengaduan_sekolah/views/auth/login.php");
    exit;
}

$nis = $_SESSION['nis'];

// Gunakan prepared statement untuk keamanan
$query = "SELECT a.*, k.ket_kategori FROM aspirasi a
          LEFT JOIN kategori k ON a.id_kategori = k.id_kategori
          WHERE a.nis = ? ORDER BY a.created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $nis);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);

if (!$data) {
    die("Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Aspirasi - Sistem Pengaduan Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: linear-gradient(135deg, #F4F7F6, #FDFDFD);
            font-family: 'Poppins', sans-serif;
            color: #2C3E50;
            min-height: 100vh;
        }
        .header-school {
            background: linear-gradient(135deg, #4A90E2, #357ABD);
            color: white;
            padding: 28px 0;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(74, 144, 226, 0.15);
        }
        .header-school h1 {
            font-size: 2.4rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .header-school p {
            opacity: 0.95;
            font-size: 1.05rem;
            margin: 0;
        }
        .container {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            padding: 35px;
            margin-bottom: 30px;
            border: 1px solid rgba(168, 178, 193, 0.15);
        }
        .table-responsive {
            border-radius: 16px;
            overflow: hidden;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            background: linear-gradient(135deg, #4A90E2, #357ABD);
            color: white;
            border: none;
            font-weight: 600;
            padding: 18px 15px;
        }
        .table td {
            vertical-align: middle;
            padding: 16px 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .table tbody tr {
            transition: all 0.3s ease;
        }
        .table tbody tr:hover {
            background-color: rgba(74, 144, 226, 0.04);
        }
        .btn-warning {
            background: linear-gradient(135deg, #D4AF37, #b8951f);
            border: none;
            color: white;
        }
        .btn-warning:hover {
            background: linear-gradient(135deg, #b8951f, #D4AF37);
            color: white;
        }
        .btn-danger {
            background: linear-gradient(135deg, #FF6B6B, #ee5a52);
            border: none;
            color: white;
        }
        .btn-danger:hover {
            background: linear-gradient(135deg, #ee5a52, #FF6B6B);
            color: white;
        }
        .btn-secondary {
            background: #f4f7f6;
            border: 1px solid #d6dade;
            color: #2C3E50;
            border-radius: 12px;
            font-weight: 600;
        }
        .btn-secondary:hover {
            background: #e8eff3;
            color: #2C3E50;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        .status-badge.bg-success {
            background: linear-gradient(135deg, #10b981, #059669) !important;
        }
        .status-badge.bg-danger {
            background: linear-gradient(135deg, #FF6B6B, #ee5a52) !important;
        }
        .status-badge.bg-warning {
            background: linear-gradient(135deg, #D4AF37, #b8951f) !important;
            color: white !important;
        }
        .status-badge.bg-secondary {
            background: linear-gradient(135deg, #6b7280, #4b5563) !important;
        }
        .no-data {
            text-align: center;
            padding: 60px 40px;
            color: #6b7280;
        }
        .no-data i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #d1d5db;
        }
        .no-data h5 {
            color: #2C3E50;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .no-data a {
            color: #4A90E2;
            text-decoration: none;
            font-weight: 600;
        }
        .no-data a:hover {
            text-decoration: underline;
        }
        footer {
            background: linear-gradient(135deg, #2C3E50, #1a242f);
            color: white;
            margin-top: 40px;
        }
        .img-thumbnail {
            border-radius: 12px;
            border: 2px solid #e5e7eb;
        }
        .btn-group .btn-sm {
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.9rem;
        }
        .btn-plus {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            color: white;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
        }
        .btn-plus:hover {
            background: linear-gradient(135deg, #059669, #10b981);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);
            color: white;
        }
        h3 {
            font-weight: 700;
            color: #2C3E50;
        }
        @media (max-width: 768px) {
            .container {
                padding: 25px;
            }
            .header-school h1 {
                font-size: 1.8rem;
            }
            .table th {
                padding: 12px 10px;
                font-size: 0.9rem;
            }
            .table td {
                padding: 12px 10px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header Sekolah -->
    <div class="header-school">
        <h1><i class="fas fa-school me-2"></i>Sistem Pengaduan Sekolah</h1>
        <p>Riwayat Aspirasi Siswa</p>
    </div>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="fas fa-history me-2"></i>Riwayat Aspirasi Anda</h3>
            <a href="/pengaduan_sekolah/views/siswa/dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>

        <?php if (mysqli_num_rows($data) > 0) { ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Kategori</th>
                            <th width="30%">Keterangan</th>
                            <th width="10%">Status</th>
                            <th width="15%">Foto</th>
                            <th width="15%">Tanggal</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($data)) {
                            // Tentukan badge status
                            $status = $row['status'];
                            $badgeClass = 'secondary';
                            if ($status === 'Selesai') {
                                $badgeClass = 'success';
                            } elseif ($status === 'Proses') {
                                $badgeClass = 'warning';
                            } elseif ($status === 'Menunggu') {
                                $badgeClass = 'secondary';
                            }
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['ket_kategori'] ?? 'Tidak ada'); ?></td>
                            <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $badgeClass; ?> status-badge">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['foto'] && file_exists(__DIR__ . '/../../' . $row['foto'])) { ?>
                                    <img src="/pengaduan_sekolah/<?php echo htmlspecialchars($row['foto']); ?>"
                                         alt="Foto Aspirasi" width="80" class="img-thumbnail">
                                <?php } else { ?>
                                    <small class="text-muted">Tidak ada</small>
                                <?php } ?>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="/pengaduan_sekolah/views/siswa/edit_aspirasi.php?id=<?php echo $row['id_aspirasi']; ?>"
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/pengaduan_sekolah/controllers/siswa_controller.php?hapus=<?php echo $row['id_aspirasi']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus aspirasi ini?')"
                                       title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="no-data">
                <i class="fas fa-inbox"></i>
                <h5>Tidak ada riwayat aspirasi</h5>
                <p>Anda belum mengirimkan aspirasi apapun. <a href="/pengaduan_sekolah/views/siswa/input_aspirasi.php">Kirim aspirasi sekarang</a></p>
            </div>
        <?php } ?>
    </div>

    <!-- Footer -->
    <footer class="text-center py-3 bg-light">
        <p class="mb-0">&copy; 2026 Sistem Pengaduan Sekolah. Dibuat untuk kemajuan pendidikan.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>