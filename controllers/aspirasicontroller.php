<?php
require_once __DIR__ . "/../models/aspirasi.php";
require_once __DIR__ . "/../models/feedback.php";

class AspirasiController {

    function dashboard(){
        if(!isset($_SESSION['role']) || $_SESSION['role'] !== "admin") {
            die("Akses ditolak");
        }

        $search = $_GET['search'] ?? "";
        $kategori = $_GET['kategori'] ?? "";
        $status = $_GET['status'] ?? "";
        $page = $_GET['page'] ?? 1;

        $data = Aspirasi::getAll($search, $kategori, $status, $page);
        $statistik = Aspirasi::statistik();

        include __DIR__ . "/../views/admin/dashboard.php";
    }

    function hapus(){
        Aspirasi::delete($_GET['id']);
        header("Location: router.php?controller=admin&action=dashboard");
        exit;
    }

    function editForm(){
        include __DIR__ . "/../views/admin/edit.php";
    }

    function update(){
        Aspirasi::update(
            $_POST['id'],
            $_POST['kategori'],
            $_POST['lokasi'],
            $_POST['keterangan']
        );

        header("Location: router.php?controller=admin&action=dashboard");
        exit;
    }

    function kirimFeedback(){
        Feedback::tambah($_POST['id_aspirasi'], $_POST['pesan']);
        header("Location: router.php?controller=admin&action=dashboard");
        exit;
    }
}