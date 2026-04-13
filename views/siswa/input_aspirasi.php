<?php
session_start();
include __DIR__ . '/../../config/koneksi.php';

if (!isset($_SESSION['nis'])) {
    header("Location: /pengaduan_sekolah/views/auth/login.php");
    exit;
}

// Ambil kategori dari database
$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY ket_kategori ASC");
if (!$kategori) {
    die("Error mengambil kategori: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Aspirasi Baru - Sistem Pengaduan Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
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
            box-shadow: 0 20px 40px rgba(74, 144, 226, 0.18);
        }
        .header-school h1 {
            font-size: 2.4rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .header-school p {
            opacity: 0.95;
            font-size: 1rem;
            margin: 0;
        }
        .container {
            background: rgba(255,255,255,0.96);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            padding: 35px;
            margin-bottom: 30px;
            border: 1px solid rgba(168,178,193,0.18);
        }
        .form-label {
            font-weight: 600;
            color: #2C3E50;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        .form-label i {
            color: #4A90E2;
            margin-right: 6px;
        }
        .form-control {
            border-radius: 15px;
            border: 1px solid #d6dade;
            background: #f8fafc;
            padding: 16px 18px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #4A90E2;
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.15);
            background: #ffffff;
        }
        .form-control, .form-select {
            color: #2C3E50;
        }
        .form-select {
            border-radius: 15px;
            border: 1px solid #d6dade;
            background: #f8fafc;
            padding: 16px 18px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }
        .form-select:focus {
            border-color: #4A90E2;
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.15);
            background: #ffffff;
        }
        .btn-school {
            background: linear-gradient(135deg, #4A90E2, #357ABD);
            border-color: transparent;
            border-radius: 12px;
            padding: 14px 24px;
            font-weight: 600;
        }
        .btn-school:hover {
            background: linear-gradient(135deg, #357ABD, #4A90E2);
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(74, 144, 226, 0.25);
        }
        .btn-secondary {
            border-radius: 12px;
            padding: 14px 24px;
            font-weight: 600;
            background: #f4f7f6;
            border-color: #d6dade;
            color: #2C3E50;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: #e8eff3;
            color: #2C3E50;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }
        .btn-cancel {
            border-radius: 12px;
            padding: 14px 24px;
            font-weight: 600;
            background: #FF6B6B;
            border-color: transparent;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-cancel:hover {
            background: #ee5a52;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 107, 107, 0.3);
            color: white;
        }
        .preview-img {
            border-radius: 16px;
            box-shadow: 0 16px 35px rgba(0,0,0,0.08);
            max-width: 100%;
            height: auto;
        }
        .alert-info {
            background: rgba(74, 144, 226, 0.1);
            border-color: rgba(74, 144, 226, 0.2);
            color: #2C3E50;
        }
        .alert-info i {
            color: #4A90E2;
        }
        .form-text {
            color: #6b7280;
            font-size: 0.85rem;
            margin-top: 6px;
            display: block;
        }
        .d-flex.gap-2.justify-content-end {
            flex-wrap: wrap;
            gap: 12px !important;
        }
        .form-control::placeholder {
            color: #94a3b8;
        }
        .footer-style {
            background: linear-gradient(135deg, #2C3E50, #1a242f);
            color: white;
            padding: 30px 0;
            margin-top: 50px;
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.08);
        }
        .footer-style p {
            margin: 0;
            font-weight: 500;
        }
        @media (max-width: 768px) {
            .container {
                padding: 25px;
            }
            .header-school h1 {
                font-size: 1.9rem;
            }
            .header-school p {
                font-size: 0.95rem;
            }
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }
            .d-flex.justify-content-between a {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header Sekolah -->
    <div class="header-school">
        <h1><i class="fas fa-school me-2"></i>Sistem Pengaduan Sekolah</h1>
        <p>Kirim Aspirasi Baru</p>
    </div>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 style="color: #2C3E50; font-weight: 700; margin-bottom: 0;"><i class="fas fa-plus-circle me-2" style="color: #4A90E2;"></i>Kirim Aspirasi Baru</h3>
                <p style="color: #6b7280; margin: 0; margin-top: 5px; font-size: 0.95rem;">Sampaikan aspirasi dan masukan Anda kepada sekolah</p>
            </div>
            <a href="/pengaduan_sekolah/views/siswa/dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Informasi:</strong> Isilah form di bawah ini untuk mengirim aspirasi Anda. Pastikan data yang dimasukkan akurat dan sopan.
        </div>

        <form method="POST" enctype="multipart/form-data" action="/pengaduan_sekolah/controllers/siswa_controller.php" id="aspirasiForm">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="id_kategori" class="form-label">
                        <i class="fas fa-tag"></i>Kategori Aspirasi
                    </label>
                    <select name="id_kategori" id="id_kategori" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($row = mysqli_fetch_assoc($kategori)): ?>
                            <option value="<?php echo htmlspecialchars($row['id_kategori']); ?>">
                                <?php echo htmlspecialchars($row['ket_kategori']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="lokasi" class="form-label">
                        <i class="fas fa-map-marker-alt"></i>Lokasi (Opsional)
                    </label>
                    <input type="text" name="lokasi" id="lokasi" class="form-control" placeholder="Contoh: Ruang Kelas 1A, Kantin, Lapangan">
                    <div class="form-text">
                        <small>Isi lokasi kejadian atau area terkait aspirasi jika tersedia.</small>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="foto" class="form-label">
                        <i class="fas fa-camera"></i>Foto Pendukung (Opsional)
                    </label>
                    <input type="file" name="foto" id="foto" class="form-control" accept="image/*">
                    <div class="form-text">
                        <small>Format: JPG, PNG, GIF. Maksimal 2MB.</small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">
                    <i class="fas fa-comment"></i>Keterangan Aspirasi
                </label>
                <textarea name="keterangan" id="keterangan" class="form-control" rows="5"
                          placeholder="Jelaskan aspirasi Anda dengan detail..." required></textarea>
                <div class="form-text">
                    <small>Minimal 10 karakter, maksimal 1000 karakter.</small>
                </div>
            </div>

            <div id="preview-container" class="mb-3" style="display: none;">
                <label class="form-label">Pratinjau Foto:</label><br>
                <img id="preview" class="preview-img" alt="Pratinjau foto">
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                    <i class="fas fa-undo me-2"></i>Reset
                </button>
                <button type="submit" class="btn btn-school">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Aspirasi
                </button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="footer-style text-center">
        <p>&copy; 2026 Sistem Pengaduan Sekolah. Dibuat untuk kemajuan pendidikan.</p>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('foto').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('preview-container');

    if (file) {
        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Tipe file tidak didukung. Hanya JPG, PNG, dan GIF yang diperbolehkan.');
            this.value = '';
            previewContainer.style.display = 'none';
            return;
        }

        // Validasi ukuran file (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 2MB.');
            this.value = '';
            previewContainer.style.display = 'none';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
    }
});

// Validasi form sebelum submit
document.getElementById('aspirasiForm').addEventListener('submit', function(e) {
    const keterangan = document.getElementById('keterangan').value.trim();
    if (keterangan.length < 10) {
        e.preventDefault();
        alert('Keterangan aspirasi minimal 10 karakter.');
        return;
    }
    if (keterangan.length > 1000) {
        e.preventDefault();
        alert('Keterangan aspirasi maksimal 1000 karakter.');
        return;
    }
});

// Fungsi reset form
function resetForm() {
    if (confirm('Apakah Anda yakin ingin mereset form? Data yang sudah diisi akan hilang.')) {
        document.getElementById('aspirasiForm').reset();
        document.getElementById('preview-container').style.display = 'none';
    }
}

// Tambahkan validasi real-time untuk textarea
document.getElementById('keterangan').addEventListener('input', function() {
    const length = this.value.length;
    const minLength = 10;
    const maxLength = 1000;

    if (length < minLength) {
        this.setCustomValidity(`Minimal ${minLength} karakter. Saat ini: ${length}`);
    } else if (length > maxLength) {
        this.setCustomValidity(`Maksimal ${maxLength} karakter. Saat ini: ${length}`);
    } else {
        this.setCustomValidity('');
    }
});
</script>
</body>
</html>