<?php
session_start();
session_unset();
session_destroy();
header("Location: /pengaduan_sekolah/views/auth/login.php");
exit;