<?php
session_start();
require_once 'config/database.php';

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

$kategori = $resultCek->fetch_assoc();
$namaKategori = $kategori['nama_kategori'];
$stmtCek->close();

$stmtDelete = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
$stmtDelete->bind_param("i", $id);

if ($stmtDelete->execute()) {
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

header("Location: index.php");
exit;
?>
