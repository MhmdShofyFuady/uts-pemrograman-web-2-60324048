<?php
session_start();
require_once 'config/database.php';

// -------------------------------------------------------
// Validasi ID dari GET
// -------------------------------------------------------
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setFlash('error', 'Parameter ID tidak valid atau tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

if ($id <= 0) {
    setFlash('error', 'ID kategori tidak valid.');
    header("Location: index.php");
    exit;
}

// -------------------------------------------------------
// Cek keberadaan data di database sebelum dihapus
// -------------------------------------------------------
$stmtCek = $conn->prepare("SELECT id_kategori, nama_kategori FROM kategori WHERE id_kategori = ?");
$stmtCek->bind_param("i", $id);
$stmtCek->execute();
$resultCek = $stmtCek->get_result();

if ($resultCek->num_rows === 0) {
    $stmtCek->close();
    setFlash('error', 'Kategori tidak ditemukan atau sudah dihapus.');
    header("Location: index.php");
    exit;
}

// Ambil nama kategori untuk pesan konfirmasi
$kategori = $resultCek->fetch_assoc();
$namaKategori = $kategori['nama_kategori'];
$stmtCek->close();

// -------------------------------------------------------
// Proses DELETE dengan prepared statement
// -------------------------------------------------------
$stmtDelete = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
$stmtDelete->bind_param("i", $id);

if ($stmtDelete->execute()) {
    // Pastikan benar-benar terhapus (affected_rows > 0)
    if ($stmtDelete->affected_rows > 0) {
        setFlash('success', "Kategori <strong>{$namaKategori}</strong> berhasil dihapus.");
    } else {
        setFlash('error', 'Tidak ada data yang terhapus. Silakan coba lagi.');
    }
} else {
    setFlash('error', 'Gagal menghapus data: ' . $conn->error);
}

$stmtDelete->close();
$conn->close();

// -------------------------------------------------------
// Redirect kembali ke index
// -------------------------------------------------------
header("Location: index.php");
exit;
?>
