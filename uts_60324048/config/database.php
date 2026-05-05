<?php

define('DB_SERVER',   'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME',     'uts_perpustakaan_60324048');

// Buat koneksi MySQLi
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Cek apakah koneksi berhasil
if ($conn->connect_error) {
    die("<div style='font-family:sans-serif;padding:20px;color:red;'>
         <strong>Koneksi Gagal!</strong><br>" . $conn->connect_error . "
         </div>");
}

// Set charset UTF-8 agar karakter Indonesia aman
$conn->set_charset("utf8mb4");

// =========================================
// Helper Functions
// =========================================

/**
 * Sanitasi input dari user — cegah XSS
 */
function escape($conn, $data) {
    return htmlspecialchars($conn->real_escape_string(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Set pesan flash ke session (sukses / error)
 */
function setFlash($type, $message) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Ambil dan hapus pesan flash dari session
 */
function getFlash() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>
