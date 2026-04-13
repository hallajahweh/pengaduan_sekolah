<?php
session_start();
require_once "../../config/koneksi.php";

$error = '';
$old = [];

if (isset($_POST['register'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role     = $_POST['role'];
    $nis      = $_POST['nis'] ?? '';
    $nama     = $_POST['nama'] ?? '';
    $kelas    = $_POST['kelas'] ?? '';

    // VALIDASI
    if ($username == '' || $password == '' || $role == '') {
        $error = "Semua field wajib diisi!";
    } elseif (strlen($username) < 3) {
        $error = "Username minimal 3 karakter!";
    } elseif (strlen($password) < 8) {
        $error = "Password minimal 8 karakter!";
    } else {

        // cek username
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Username sudah terdaftar!";
        } else {

            // TANPA HASH
            mysqli_query($conn, "INSERT INTO users(username,password,role,nis)
                                 VALUES('$username','$password','$role','$nis')");

            // jika siswa
            if ($role == 'siswa') {
                mysqli_query($conn, "INSERT INTO siswa(nis,nama,kelas)
                                     VALUES('$nis','$nama','$kelas')");
            }

            $_SESSION['register_success'] = "Registrasi berhasil!";
            header("Location: login.php");
            exit;
        }
    }

    $old = $_POST;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Pengaduan Sarana Sekolah</title>
    <link rel="stylesheet" href="../../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid #c33;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .success-message {
            background: #e6ffed;
            color: #1f7a3b;
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid #22c55e;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .input-group {
            position: relative;
            margin-bottom: 20px;
        }
        .input-group input,
        .input-group select {
            width: 100%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #fff;
            box-sizing: border-box;
        }
        .input-group.password-wrapper {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
            align-items: center;
        }
        .input-group input:focus,
        .input-group select:focus {
            outline: none;
            border-color: #4A90E2;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        }
        .toggle-password {
            border: 2px solid #ddd;
            background: #fff;
            border-radius: 8px;
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4A90E2;
            cursor: pointer;
            transition: border-color 0.3s ease, background 0.3s ease;
        }
        .toggle-password:hover {
            border-color: #4A90E2;
            background: #f4faff;
        }
        .btn-register {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #4A90E2, #357ABD);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(74, 144, 226, 0.3);
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .login-link a {
            color: #4A90E2;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .siswa-fields {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 12px;
            border: 2px solid #e3f2fd;
            margin-top: 15px;
        }
        .siswa-fields h4 {
            margin: 0 0 15px 0;
            color: #4A90E2;
            font-size: 16px;
        }
        .siswa-fields input {
            background: #fff;
        }
    </style>
</head>

<body>
    <div class="container-login">
        <!-- Bagian Kiri - Welcome -->
        <div class="welcome">
            <h1><i class="fas fa-user-plus"></i> Selamat Bergabung</h1>
            <p>
                Buat akun untuk mengakses <strong>Sistem Pengaduan Sarana Sekolah</strong>.  
                Melalui sistem ini siswa dapat menyampaikan laporan mengenai kerusakan fasilitas 
                seperti kelas, laboratorium, bengkel, atau prasarana umum di sekolah.
            </p>
            <p>Isi data dengan benar untuk melanjutkan proses pendaftaran.</p>
        </div>

        <!-- Form Register -->
        <div class="form-container">
            <div class="logo">
                <i class="fas fa-user-plus" style="font-size: 4rem; color: #4A90E2;"></i>
            </div>
            
            <h2>Register Pengguna</h2>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="registerForm">
                <div class="input-group">
                    <input 
                        type="text" 
                        name="username" 
                        id="username"
                        placeholder="Username (min 3 karakter)" 
                        required 
                        value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                        maxlength="50"
                        minlength="3"
                    >
                </div>

                <div class="input-group password-wrapper">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="Password (min 8 karakter)" 
                        required 
                        minlength="8"
                    >
                    <button type="button" class="toggle-password" id="togglePassword" aria-label="Tampilkan password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <div class="input-group">
                    <select name="role" id="role" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" <?= ($old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
                            👨‍💼 Admin
                        </option>
                        <option value="siswa" <?= ($old['role'] ?? '') === 'siswa' ? 'selected' : '' ?>>
                            👨‍🎓 Siswa
                        </option>
                    </select>
                </div>

                <!-- Form Siswa -->
                <div id="siswaBox" class="siswa-fields" style="display:none;">
                    <h4><i class="fas fa-graduation-cap"></i> Data Siswa</h4>
                    <div class="input-group">
                        <input 
                            type="text" 
                            name="nis" 
                            placeholder="NIS Siswa" 
                            value="<?= htmlspecialchars($old['nis'] ?? '') ?>"
                            maxlength="20"
                        >
                    </div>
                    <div class="input-group">
                        <input 
                            type="text" 
                            name="nama" 
                            placeholder="Nama Lengkap Siswa" 
                            value="<?= htmlspecialchars($old['nama'] ?? '') ?>"
                            maxlength="100"
                        >
                    </div>
                    <div class="input-group">
                        <input 
                            type="text" 
                            name="kelas" 
                            placeholder="Kelas (contoh: X TKJ 1)" 
                            value="<?= htmlspecialchars($old['kelas'] ?? '') ?>"
                            maxlength="30"
                        >
                    </div>
                </div>

                <button type="submit" name="register" class="btn-register">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </button>

                <div class="login-link">
                    Sudah punya akun? 
                    <a href="login.php">Login di sini</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const roleSelect = document.getElementById('role');
            const siswaBox = document.getElementById('siswaBox');
            const togglePassword = document.getElementById('togglePassword');
            const registerForm = document.getElementById('registerForm');

            // Toggle visibility password
            togglePassword.addEventListener('click', function() {
                const isVisible = passwordInput.type === 'text';
                passwordInput.type = isVisible ? 'password' : 'text';
                this.innerHTML = isVisible ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                this.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
            });

            // Show/hide siswa fields
            roleSelect.addEventListener('change', function() {
                if (this.value === 'siswa') {
                    siswaBox.style.display = 'block';
                } else {
                    siswaBox.style.display = 'none';
                }
            });

            // Set initial state based on old data
            if (roleSelect.value === 'siswa') {
                siswaBox.style.display = 'block';
            }

            // Validasi form sebelum submit
            registerForm.addEventListener('submit', function(e) {
                const username = usernameInput.value.trim();
                const password = passwordInput.value.trim();
                const role = roleSelect.value;

                if (!username || !password || !role) {
                    e.preventDefault();
                    alert('❌ Semua field wajib diisi!');
                    return false;
                }

                if (username.length < 3) {
                    e.preventDefault();
                    alert('❌ Username minimal 3 karakter!');
                    usernameInput.focus();
                    return false;
                }

                if (password.length < 8) {
                    e.preventDefault();
                    alert('❌ Password minimal 8 karakter!');
                    passwordInput.focus();
                    return false;
                }

                if (role === 'siswa') {
                    const nis = document.querySelector('input[name="nis"]').value.trim();
                    const nama = document.querySelector('input[name="nama"]').value.trim();
                    const kelas = document.querySelector('input[name="kelas"]').value.trim();
                    
                    if (!nis || !nama || !kelas) {
                        e.preventDefault();
                        alert('❌ Semua data siswa wajib diisi!');
                        return false;
                    }
                }
            });

            // Real-time validation
            usernameInput.addEventListener('blur', function() {
                if (this.value && this.value.trim().length < 3) {
                    this.style.borderColor = '#e74c3c';
                } else {
                    this.style.borderColor = '#ddd';
                }
            });

            passwordInput.addEventListener('blur', function() {
                if (this.value && this.value.length < 8) {
                    this.style.borderColor = '#e74c3c';
                } else {
                    this.style.borderColor = '#ddd';
                }
            });
        });
    </script>
</body>
</html>