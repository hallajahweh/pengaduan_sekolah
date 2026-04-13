<?php
session_start();
require_once "../../config/koneksi.php";

// Inisialisasi variabel
$error = '';
$old_data = [];

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validasi input
    if (empty($username) || empty($password) || empty($role)) {
        $error = "Semua field harus diisi!";
    } else {
        // Prepared statement untuk keamanan
        $stmt = $conn->prepare("SELECT id, username, password, role, nis FROM users WHERE username = ? AND role = ?");
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Verifikasi password (plain text comparison)
        if ($user && $password === $user['password']) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user'] = $user;
            $_SESSION['email'] = $user['username']; // kompatibilitas backward
            
            if ($user['role'] === 'siswa') {
                $_SESSION['nis'] = $user['nis'];
            }

            // Redirect berdasarkan role
            $redirects = [
                'admin' => '../../views/admin/dashboard.php',
                'siswa' => '../../views/siswa/dashboard.php'
            ];

            if (isset($redirects[$user['role']])) {
                header("Location: " . $redirects[$user['role']]);
                exit;
            }
        } else {
            $error = "Username, Password, atau Role salah!";
        }
    }
    
    $old_data = $_POST;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pengaduan Sarana Sekolah</title>
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
        .btn-login {
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
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(74, 144, 226, 0.3);
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .register-link a {
            color: #4A90E2;
            text-decoration: none;
            font-weight: 600;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container-login">
        <!-- Bagian Kiri - Welcome -->
        <div class="welcome">
            <h1><i class="fas fa-school"></i> Selamat Datang</h1>
            <p>
                Selamat datang di <strong>Sistem Pengaduan Sarana Sekolah</strong>.  
                Melalui sistem ini siswa dapat menyampaikan laporan mengenai kerusakan fasilitas 
                seperti kelas, laboratorium, bengkel, atau prasarana umum di sekolah.
            </p>
            <p>Silakan login untuk mengakses layanan pengaduan.</p>
        </div>

        <!-- Form Login -->
        <div class="form-container">
            <div class="logo">
                <i class="fas fa-shield-alt" style="font-size: 4rem; color: #4A90E2;"></i>
            </div>
            
            <h2>Login Pengaduan</h2>

            <?php if (!empty($_SESSION['register_success'])): ?>
                <div class="error-message" style="background:#e6ffed;color:#1f7a3b;border-left-color:#22c55e;">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($_SESSION['register_success']) ?>
                </div>
                <?php unset($_SESSION['register_success']); ?>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="loginForm">
                <div class="input-group">
                    <input 
                        type="text" 
                        name="username" 
                        id="username"
                        placeholder="Username" 
                        required 
                        value="<?= htmlspecialchars($old_data['username'] ?? '') ?>"
                        maxlength="50"
                    >
                </div>

                <div class="input-group password-wrapper">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="Masukkan password" 
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
                        <option value="admin" <?= ($old_data['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
                            👨‍💼 Admin
                        </option>
                        <option value="siswa" <?= ($old_data['role'] ?? '') === 'siswa' ? 'selected' : '' ?>>
                            👨‍🎓 Siswa
                        </option>
                    </select>
                </div>

                <button type="submit" name="login" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>

                <div class="register-link">
                    Belum punya akun? 
                    <a href="register.php">Daftar di sini</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const usernameInput = document.getElementById('username');
            const loginForm = document.getElementById('loginForm');
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');

            // Toggle visibility password untuk pengalaman pengguna yang lebih baik
            togglePassword.addEventListener('click', function() {
                const isVisible = passwordInput.type === 'text';
                passwordInput.type = isVisible ? 'password' : 'text';
                this.innerHTML = isVisible ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                this.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
            });

            // Validasi username sederhana saat lose focus
            usernameInput.addEventListener('blur', function() {
                if (this.value && this.value.trim().length < 3) {
                    this.style.borderColor = '#e74c3c';
                } else {
                    this.style.borderColor = '#ddd';
                }
            });

            // Validasi form sebelum submit
            loginForm.addEventListener('submit', function(e) {
                const username = usernameInput.value.trim();
                const password = passwordInput.value.trim();
                const role = document.getElementById('role').value;

                if (!username || !password || !role) {
                    e.preventDefault();
                    alert('❌ Semua field harus diisi!');
                    return false;
                }

                if (password.length < 8) {
                    e.preventDefault();
                    alert('❌ Password minimal 8 karakter!');
                    return false;
                }
            });
        });
    </script>
</body>
</html>