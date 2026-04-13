<?php
require_once __DIR__ . "/../config/koneksi.php";

class Aspirasi {

    static function getAll($search = "", $kategori = "", $status = "", $page = 1) {
        global $conn;
        $limit = 5;
        $start = ($page - 1) * $limit;

        $where = "WHERE 1=1";
        if ($search !== "") {
            $searchEscaped = mysqli_real_escape_string($conn, $search);
            $where .= " AND keterangan LIKE '%$searchEscaped%'";
        }
        if ($kategori !== "") {
            $kategoriEscaped = mysqli_real_escape_string($conn, $kategori);
            $where .= " AND id_kategori = '$kategoriEscaped'";
        }
        if ($status !== "") {
            $statusEscaped = mysqli_real_escape_string($conn, $status);
            $where .= " AND status = '$statusEscaped'";
        }

        return mysqli_query($conn, "SELECT * FROM aspirasi $where ORDER BY id_aspirasi DESC LIMIT $start,$limit");
    }

    static function count() {
        global $conn;
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM aspirasi");
        return mysqli_fetch_assoc($result)['total'];
    }

    static function tambah($nis, $id_kategori, $lokasi, $keterangan) {
        global $conn;
        $nisEscaped = mysqli_real_escape_string($conn, $nis);
        $kategoriEscaped = mysqli_real_escape_string($conn, $id_kategori);
        $lokasiEscaped = mysqli_real_escape_string($conn, $lokasi);
        $keteranganEscaped = mysqli_real_escape_string($conn, $keterangan);
        return mysqli_query($conn, "INSERT INTO aspirasi (nis, id_kategori, lokasi, keterangan, status) VALUES ('$nisEscaped', '$kategoriEscaped', '$lokasiEscaped', '$keteranganEscaped', 'Menunggu')");
    }

    static function delete($id) {
        global $conn;
        $idEscaped = mysqli_real_escape_string($conn, $id);
        return mysqli_query($conn, "DELETE FROM aspirasi WHERE id_aspirasi = '$idEscaped'");
    }

    static function update($id, $id_kategori, $lokasi, $keterangan) {
        global $conn;
        $idEscaped = mysqli_real_escape_string($conn, $id);
        $kategoriEscaped = mysqli_real_escape_string($conn, $id_kategori);
        $lokasiEscaped = mysqli_real_escape_string($conn, $lokasi);
        $keteranganEscaped = mysqli_real_escape_string($conn, $keterangan);
        return mysqli_query($conn, "UPDATE aspirasi SET id_kategori = '$kategoriEscaped', lokasi = '$lokasiEscaped', keterangan = '$keteranganEscaped' WHERE id_aspirasi = '$idEscaped'");
    }

    static function statistik() {
        global $conn;
        return mysqli_query($conn, "SELECT status, COUNT(*) jumlah FROM aspirasi GROUP BY status");
    }
}
