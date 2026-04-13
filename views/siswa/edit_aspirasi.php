<?php
session_start();
include __DIR__ . '/../../config/koneksi.php';

if (!isset($_SESSION['nis'])) {
    header("Location: /pengaduan_sekolah/views/auth/login.php");
    exit;
}

$nis = $_SESSION['nis'];
$id_aspirasi = $_GET['id'] ?? '';

if (!$id_aspirasi || !is_numeric($id_aspirasi)) {
    header("Location: /pengaduan_sekolah/views/siswa/histori.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM aspirasi WHERE id_aspirasi = ? AND nis = ?");
mysqli_stmt_bind_param($stmt, "is", $id_aspirasi, $nis);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$aspirasi = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$aspirasi) {
    echo "Aspirasi tidak ditemukan atau Anda tidak memiliki akses!";
    exit;
}

$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY ket_kategori ASC");

if (isset($_POST['submit'])) {
    $id_kategori = trim($_POST['id_kategori'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if (empty($id_kategori) || empty($keterangan)) {
        $error = "Kategori dan keterangan harus diisi!";
    } else {
        $foto = $aspirasi['foto'];

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto = 'uploads/' . time() . '_' . $nis . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/../../' . $foto);
        }

        $stmt = mysqli_prepare($conn, "UPDATE aspirasi SET id_kategori=?, lokasi=?, keterangan=?, foto=? WHERE id_aspirasi=?");
        mysqli_stmt_bind_param($stmt, "isssi", $id_kategori, $lokasi, $keterangan, $foto, $id_aspirasi);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: histori.php");
            exit;
        } else {
            $error = "Gagal update!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Aspirasi</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #F4F7F6, #FDFDFD);
    font-family: 'Poppins', sans-serif;
    color: #2C3E50;
}

.container {
    background: rgba(255,255,255,0.95);
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(74,144,226,0.15);
    padding: 30px;
    margin-top: 50px;
}

h2 {
    color: #4A90E2;
    font-weight: 700;
}

.btn-primary {
    background: linear-gradient(135deg, #4A90E2, #357ABD);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #357ABD, #2c6bb2);
}

.btn-outline-primary {
    border: 2px solid #4A90E2;
    color: #4A90E2;
}

.btn-outline-primary:hover {
    background: #4A90E2;
    color: white;
}

.btn-secondary {
    background: #6c757d;
}

.form-control {
    border-radius: 10px;
    border: 2px solid #ddd;
}

.form-control:focus {
    border-color: #4A90E2;
    box-shadow: 0 0 0 3px rgba(74,144,226,0.1);
}

.preview-img {
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(74,144,226,0.15);
}
</style>

</head>
<body>

<div class="container">

<h2><i class="fas fa-edit"></i> Edit Aspirasi</h2>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">
<label>Kategori</label>
<select name="id_kategori" class="form-control">
<option value="">-- Pilih --</option>
<?php while($row = mysqli_fetch_assoc($kategori)): ?>
<option value="<?= $row['id_kategori'] ?>" <?= $row['id_kategori']==$aspirasi['id_kategori']?'selected':'' ?>>
<?= $row['ket_kategori'] ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="mb-3">
<label>Lokasi</label>
<input type="text" name="lokasi" class="form-control" value="<?= $aspirasi['lokasi'] ?>">
</div>

<div class="mb-3">
<label>Keterangan</label>
<textarea name="keterangan" class="form-control"><?= $aspirasi['keterangan'] ?></textarea>
</div>

<div class="mb-3">
<label>Foto</label>
<input type="file" name="foto" class="form-control">

<?php if($aspirasi['foto']): ?>
<img src="/pengaduan_sekolah/<?= $aspirasi['foto'] ?>" width="200" class="preview-img mt-2">
<?php endif; ?>
</div>

<button type="submit" name="submit" class="btn btn-primary">
<i class="fas fa-save"></i> Update
</button>

<a href="histori.php" class="btn btn-secondary">Batal</a>

</form>

</div>

</body>
</html>