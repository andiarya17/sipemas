<?php
require_once 'config.php';

// Fungsi untuk membersihkan input
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fungsi untuk redirect
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Fungsi untuk mengecek login admin
function check_admin_login() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        redirect(BASE_URL . 'login.php');
    }
}

// Fungsi untuk mengecek login user
function check_user_login() {
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        redirect(BASE_URL . 'login.php');
    }
}

// Fungsi untuk upload file
function upload_file($file, $target_dir = 'assets/uploads/') {
    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }
    
    // Check file size (max 5MB)
    if ($file["size"] > 5000000) {
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        return false;
    } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return basename($file["name"]);
        } else {
            return false;
        }
    }
}

// Fungsi untuk menghitung statistik pengaduan
function get_pengaduan_stats() {
    global $pdo;
    
    $stats = array();
    
    // Total pengaduan
    $stmt = $pdo->query("SELECT COUNT(*) FROM tbl_pengaduan");
    $stats['total'] = $stmt->fetchColumn();
    
    // Pengaduan pending
    $stmt = $pdo->query("SELECT COUNT(*) FROM tbl_pengaduan WHERE status = 'pending'");
    $stats['pending'] = $stmt->fetchColumn();
    
    // Pengaduan dalam proses
    $stmt = $pdo->query("SELECT COUNT(*) FROM tbl_pengaduan WHERE status = 'process'");
    $stats['process'] = $stmt->fetchColumn();
    
    // Pengaduan selesai
    $stmt = $pdo->query("SELECT COUNT(*) FROM tbl_pengaduan WHERE status = 'done'");
    $stats['done'] = $stmt->fetchColumn();
    
    return $stats;
}

// Fungsi untuk mendapatkan pengaduan berdasarkan NIK
function get_pengaduan_by_nik($nik) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM tbl_pengaduan WHERE NIK = ? ORDER BY created_at DESC");
    $stmt->execute([$nik]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi untuk mendapatkan semua pengaduan (admin)
function get_all_pengaduan() {
    global $pdo;
    
    $stmt = $pdo->query("SELECT p.*, m.nama FROM tbl_pengaduan p 
                        JOIN tbl_masyarakat m ON p.NIK = m.NIK 
                        ORDER BY p.created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi untuk update status pengaduan
function update_pengaduan_status($id_pengaduan, $status) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE tbl_pengaduan SET status = ? WHERE id_pengaduan = ?");
    return $stmt->execute([$status, $id_pengaduan]);
}

// Fungsi untuk menambah tanggapan
function add_tanggapan($id_pengaduan, $tanggapan, $admin_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("INSERT INTO tbl_tanggapan (id_pengaduan, tanggal_tanggapan, tanggapan, admin_id) 
                          VALUES (?, CURDATE(), ?, ?)");
    return $stmt->execute([$id_pengaduan, $tanggapan, $admin_id]);
}
?>