require_once __DIR__ . "/../config/koneksi.php";
require_once __DIR__ . "/../models/feedback.php";

class AdminController {

    // tampil
    public function feedback(){
        if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
            die('Akses ditolak');
        }

        $data = Feedback::getAll();
        include __DIR__ . "/../views/admin/feedback.php";
    }

    // tambah
    public function tambahFeedback(){
        if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
            die('Akses ditolak');
        }

        if(isset($_POST['simpan'])){
            Feedback::tambah($_POST['id_aspirasi'], $_POST['feedback']);
            header("Location: router.php?controller=admin&action=feedback");
            exit;
        }

        // ambil aspirasi untuk dropdown
        global $conn;
        $aspirasi = mysqli_query($conn, "SELECT * FROM aspirasi");
        include __DIR__ . "/../views/admin/tambah_feedback.php";
    }

    // hapus
    public function hapusFeedback(){
        if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
            die('Akses ditolak');
        }

        Feedback::hapus($_GET['id']);
        header("Location: router.php?controller=admin&action=feedback");
        exit;
    }

    // edit
    public function editFeedback(){
        if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
            die('Akses ditolak');
        }

        $data = Feedback::getById($_GET['id']);

        if(isset($_POST['update'])){
            Feedback::update($_GET['id'], $_POST['feedback']);
            header("Location: router.php?controller=admin&action=feedback");
            exit;
        }

        include __DIR__ . "/../views/admin/edit_feedback.php";
    }
}