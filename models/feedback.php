<?php
require_once __DIR__ . "/../config/koneksi.php";

class Feedback {

    public function __construct($conn = null){
        // Constructor kept for backward compatibility with older controller usage.
    }

    public static function getAll(){
        global $conn;
        $query = "
        SELECT 
            f.id_feedback,
            f.feedback,
            f.created_at,
            a.id_aspirasi,
            a.nis,
            a.keterangan AS aspirasi
        FROM feedback f
        JOIN aspirasi a ON f.id_aspirasi = a.id_aspirasi
        ORDER BY f.id_feedback DESC
        ";
        return mysqli_query($conn, $query);
    }

    public static function tambah($id_aspirasi, $isi){
        global $conn;
        $idEscaped = mysqli_real_escape_string($conn, $id_aspirasi);
        $isiEscaped = mysqli_real_escape_string($conn, $isi);
        return mysqli_query($conn,
        "INSERT INTO feedback(id_aspirasi, feedback) VALUES('$idEscaped','$isiEscaped')");
    }

    public static function hapus($id){
        global $conn;
        $idEscaped = mysqli_real_escape_string($conn, $id);
        return mysqli_query($conn,
        "DELETE FROM feedback WHERE id_feedback='$idEscaped'");
    }

    public static function getById($id){
        global $conn;
        $idEscaped = mysqli_real_escape_string($conn, $id);
        return mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT * FROM feedback WHERE id_feedback='$idEscaped'"));
    }

    public static function update($id, $isi){
        global $conn;
        $idEscaped = mysqli_real_escape_string($conn, $id);
        $isiEscaped = mysqli_real_escape_string($conn, $isi);
        return mysqli_query($conn,
        "UPDATE feedback SET feedback='$isiEscaped' WHERE id_feedback='$idEscaped'");
    }
}
