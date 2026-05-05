<?php
session_start();
require_once 'config/database.php';

// -------------------------------------------------------
// Query: Ambil semua data kategori (terbaru di atas)
// Menggunakan prepared statement untuk keamanan
// -------------------------------------------------------
$stmt = $conn->prepare("SELECT id_kategori, kode_kategori, nama_kategori, deskripsi, status, created_at FROM kategori ORDER BY id_kategori DESC");
$stmt->execute();
$result = $stmt->get_result();

// Ambil pesan flash dari session
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Kategori Buku — UTS Perpustakaan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    body { background-color: #f0f4f8; }
    .navbar-brand { font-weight: 700; letter-spacing: .5px; }
    .card { border: none; box-shadow: 0 2px 12px rgba(0,0,0,.08); border-radius: 12px; }
    .card-header { background: linear-gradient(135deg, #0d6efd, #0a58ca); color: #fff; border-radius: 12px 12px 0 0 !important; }
    .table thead th { background-color: #e9ecef; font-weight: 600; font-size: .85rem; text-transform: uppercase; letter-spacing: .5px; }
    .badge { font-size: .78rem; padding: .4em .75em; border-radius: 20px; }
    .btn-action { border-radius: 6px; font-size: .82rem; padding: .3rem .7rem; }
    .empty-state { padding: 50px 0; color: #adb5bd; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <i class="bi bi-book-half me-2"></i>UTS Perpustakaan
    </a>
    <span class="navbar-text text-white-50 small">Sistem Manajemen Kategori Buku</span>
  </div>
</nav>

<div class="container mt-4 mb-5">

  <!-- Flash Message -->
  <?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
    <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
    <span><?= htmlspecialchars($flash['message']) ?></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-0 fw-bold"><i class="bi bi-tags me-2 text-primary"></i>Daftar Kategori Buku</h4>
      <p class="text-muted small mb-0">Total: <strong><?= $result->num_rows ?></strong> kategori terdaftar</p>
    </div>
    <a href="create.php" class="btn btn-primary btn-sm px-3">
      <i class="bi bi-plus-lg me-1"></i>Tambah Kategori
    </a>
  </div>

  <!-- Tabel Data -->
  <div class="card">
    <div class="card-header py-3 d-flex align-items-center gap-2">
      <i class="bi bi-table"></i>
      <span class="fw-semibold">Data Kategori</span>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead>
            <tr>
              <th class="text-center" style="width:55px;">No</th>
              <th style="width:120px;">Kode</th>
              <th>Nama Kategori</th>
              <th>Deskripsi</th>
              <th class="text-center" style="width:110px;">Status</th>
              <th class="text-center" style="width:160px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0):
                  $no = 1;
                  while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td class="text-center text-muted"><?= $no++ ?></td>
              <td>
                <span class="badge bg-secondary"><?= htmlspecialchars($row['kode_kategori']) ?></span>
              </td>
              <td class="fw-semibold"><?= htmlspecialchars($row['nama_kategori']) ?></td>
              <td class="text-muted small">
                <?php
                  $desk = htmlspecialchars($row['deskripsi'] ?? '');
                  echo $desk !== '' ? (mb_strlen($desk) > 80 ? mb_substr($desk, 0, 80) . '…' : $desk) : '<em class="text-muted">—</em>';
                ?>
              </td>
              <td class="text-center">
                <?php if ($row['status'] === 'Aktif'): ?>
                  <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                <?php else: ?>
                  <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Nonaktif</span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <a href="edit.php?id=<?= $row['id_kategori'] ?>" class="btn btn-warning btn-action me-1" title="Edit">
                  <i class="bi bi-pencil-fill"></i> Edit
                </a>
                <button onclick="confirmDelete(<?= $row['id_kategori'] ?>, '<?= htmlspecialchars($row['nama_kategori'], ENT_QUOTES) ?>')"
                        class="btn btn-danger btn-action" title="Hapus">
                  <i class="bi bi-trash-fill"></i> Hapus
                </button>
              </td>
            </tr>
            <?php endwhile; else: ?>
            <tr>
              <td colspan="6" class="text-center empty-state">
                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                Belum ada data kategori. <a href="create.php">Tambah sekarang</a>
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function confirmDelete(id, nama) {
    if (confirm('Yakin ingin menghapus kategori "' + nama + '"?\nData yang dihapus tidak dapat dikembalikan!')) {
      window.location.href = 'delete.php?id=' + id;
    }
  }
</script>
</body>
</html>
<?php $stmt->close(); $conn->close(); ?>
