<?php
session_start();
include __DIR__ . '/../config/koneksi.php';

if(!isset($_SESSION['nis'])){
    header("Location: /pengaduan_sekolah/views/auth/login.php");
    exit;
}

$nis = $_SESSION['nis'];

// Kirim Aspirasi
if(isset($_POST['keterangan'])){
    $id_kategori = $_POST['id_kategori'] ?? '';
    $lokasi = trim($_POST['lokasi'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if(empty($id_kategori) || !is_numeric($id_kategori)){
        die("Kategori aspirasi tidak valid.");
    }

    if(strlen($keterangan) < 10){
        die("Keterangan aspirasi minimal 10 karakter.");
    }

    $foto = null;
    if(isset($_FILES['foto']) && $_FILES['foto']['name'] != ''){
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if(!in_array($_FILES['foto']['type'], $allowedTypes)){
            die("Tipe file tidak didukung. Hanya JPG, PNG, dan GIF yang diperbolehkan.");
        }
        if($_FILES['foto']['size'] > 2 * 1024 * 1024){
            die("Ukuran file terlalu besar. Maksimal 2MB.");
        }

        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = 'uploads/'.time().'_'.$nis.'.'.$ext;
        if(!move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/../' . $foto)){
            die("Gagal mengupload foto.");
        }
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO aspirasi (nis, id_kategori, lokasi, keterangan, foto, status, created_at) VALUES (?, ?, ?, ?, ?, 'Menunggu', NOW())");
    mysqli_stmt_bind_param($stmt, "sisss", $nis, $id_kategori, $lokasi, $keterangan, $foto);
    if(mysqli_stmt_execute($stmt)){
        mysqli_stmt_close($stmt);
        header("Location: /pengaduan_sekolah/views/siswa/dashboard.php");
        exit;
    } else {
        die("Gagal kirim aspirasi: " . mysqli_error($conn));
    }
}

// Hapus Aspirasi
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    $stmt = mysqli_prepare($conn, "DELETE FROM aspirasi WHERE id_aspirasi = ? AND nis = ?");
    mysqli_stmt_bind_param($stmt, "is", $id, $nis);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: /pengaduan_sekolah/views/siswa/histori.php");
    exit;
}

// Update Aspirasi
if(isset($_POST['update'])){
    $id = $_POST['id_aspirasi'];
    $id_kategori = $_POST['id_kategori'] ?? '';
    $lokasi = trim($_POST['lokasi'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');

    if(empty($id_kategori) || !is_numeric($id_kategori)){
        die("Kategori aspirasi tidak valid.");
    }

    if(strlen($keterangan) < 10){
        die("Keterangan aspirasi minimal 10 karakter.");
    }

    $stmt = mysqli_prepare($conn, "UPDATE aspirasi SET id_kategori = ?, lokasi = ?, keterangan = ? WHERE id_aspirasi = ? AND nis = ?");
    mysqli_stmt_bind_param($stmt, "issis", $id_kategori, $lokasi, $keterangan, $id, $nis);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: /pengaduan_sekolah/views/siswa/histori.php");
    exit;
}
?>