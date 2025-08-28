<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sipemas');

// Koneksi ke database
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("set names utf8");
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Fungsi untuk memulai session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Base URL
define('BASE_URL', 'http://localhost/sipemas/');
?>