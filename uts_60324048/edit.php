<?php
session_start();
require_once 'config/database.php';

// -------------------------------------------------------
// Ambil ID dari GET dan validasi
// -------------------------------------------------------
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    setFlash('error', 'ID kategori tidak valid.');
    header("Location: index.php");
    exit;
}

// -------------------------------------------------------
// Retrieve data kategori berdasarkan ID
// -------------------------------------------------------
$stmtGet = $conn->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
$stmtGet->bind_param("i", $id);
$stmtGet->execute();
$resultGet = $stmtGet->get_result();

if ($resultGet->num_rows === 0) {
    setFlash('error', 'Kategori tidak ditemukan.');
    header("Location: index.php");
    exit;
}

// Pre-fill variabel dari data yang ada di database
$data      = $resultGet->fetch_assoc();
$stmtGet->close();

$errors    = [];
$kode      = $data['kode_kategori'];
$nama      = $data['nama_kategori'];
$deskripsi = $data['deskripsi'] ?? '';
$status    = $data['status'];

// -------------------------------------------------------
// Proses form UPDATE ketika dikirim (POST)
// -------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil dan sanitasi input
    $kode      = trim($_POST['kode_kategori'] ?? '');
    $nama      = trim($_POST['nama_kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status    = trim($_POST['status'] ?? '');

    // -------------------------------------------------------
    // VALIDASI: kode_kategori
    // -------------------------------------------------------
    if ($kode === '') {
        $errors['kode'] = 'Kode Kategori wajib diisi.';
    } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
        $errors['kode'] = 'Kode Kategori harus antara 4–10 karakter.';
    } elseif (!preg_match('/^KAT-\d+$/i', $kode)) {
        $errors['kode'] = 'Format kode harus diawali "KAT-" diikuti angka. Contoh: KAT-001';
    } else {
        // Cek duplikasi kode — EXCLUDE record yang sedang diedit (WHERE ... AND id_kategori != ?)
        $stmtCek = $conn->prepare(
            "SELECT id_kategori FROM kategori WHERE kode_kategori = ? AND id_kategori != ?"
        );
        $stmtCek->bind_param("si", $kode, $id);
        $stmtCek->execute();
        $stmtCek->store_result();
        if ($stmtCek->num_rows > 0) {
            $errors['kode'] = 'Kode Kategori sudah digunakan oleh kategori lain.';
        }
        $stmtCek->close();
    }

    // -------------------------------------------------------
    // VALIDASI: nama_kategori
    // -------------------------------------------------------
    if ($nama === '') {
        $errors['nama'] = 'Nama Kategori wajib diisi.';
    } elseif (mb_strlen($nama) < 3) {
        $errors['nama'] = 'Nama Kategori minimal 3 karakter.';
    } elseif (mb_strlen($nama) > 50) {
        $errors['nama'] = 'Nama Kategori maksimal 50 karakter.';
    }

    // -------------------------------------------------------
    // VALIDASI: deskripsi (opsional)
    // -------------------------------------------------------
    if ($deskripsi !== '' && mb_strlen($deskripsi) > 200) {
        $errors['deskripsi'] = 'Deskripsi maksimal 200 karakter.';
    }

    // -------------------------------------------------------
    // VALIDASI: status
    // -------------------------------------------------------
    if (!in_array($status, ['Aktif', 'Nonaktif'])) {
        $errors['status'] = 'Status tidak valid.';
    }

    // -------------------------------------------------------
    // Jika tidak ada error → UPDATE database
    // -------------------------------------------------------
    if (empty($errors)) {
        $deskNull = $deskripsi === '' ? null : $deskripsi;

        $stmtUpdate = $conn->prepare(
            "UPDATE kategori SET kode_kategori = ?, nama_kategori = ?, deskripsi = ?, status = ? WHERE id_kategori = ?"
        );
        $stmtUpdate->bind_param("ssssi", $kode, $nama, $deskNull, $status, $id);

        if ($stmtUpdate->execute() && $stmtUpdate->affected_rows >= 0) {
            setFlash('success', "Kategori <strong>{$nama}</strong> berhasil diperbarui!");
            header("Location: index.php");
            exit;
        } else {
            $errors['global'] = 'Gagal memperbarui data. Silakan coba lagi.';
        }
        $stmtUpdate->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Kategori — UTS Perpustakaan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    body { background-color: #f0f4f8; }
    .card { border: none; box-shadow: 0 2px 12px rgba(0,0,0,.08); border-radius: 12px; }
    .card-header { background: linear-gradient(135deg, #ffc107, #e0a800); color: #212529; border-radius: 12px 12px 0 0 !important; }
    .form-label { font-weight: 600; font-size: .88rem; }
    .char-counter { font-size: .75rem; color: #6c757d; }
    .badge-id { background: rgba(0,0,0,.1); color: #333; border-radius: 6px; padding: .2em .6em; font-size: .78rem; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <i class="bi bi-book-half me-2"></i>UTS Perpustakaan
    </a>
  </div>
</nav>

<div class="container mt-4 mb-5">
  <div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">

      <!-- Breadcrumb -->
      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Daftar Kategori</a></li>
          <li class="breadcrumb-item active">Edit Kategori</li>
        </ol>
      </nav>

      <div class="card">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
          <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Kategori</h5>
          <span class="badge-id">ID: <?= $id ?></span>
        </div>
        <div class="card-body p-4">

          <!-- Global Error -->
          <?php if (!empty($errors['global'])): ?>
          <div class="alert alert-danger d-flex gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= $errors['global'] ?>
          </div>
          <?php endif; ?>

          <?php if (count($errors) > 0 && !isset($errors['global'])): ?>
          <div class="alert alert-warning d-flex gap-2 align-items-start">
            <i class="bi bi-exclamation-circle-fill mt-1"></i>
            <div>Terdapat <strong><?= count($errors) ?> kesalahan</strong>. Silakan periksa kembali.</div>
          </div>
          <?php endif; ?>

          <form method="POST" novalidate>

            <!-- Kode Kategori -->
            <div class="mb-3">
              <label for="kode_kategori" class="form-label">
                Kode Kategori <span class="text-danger">*</span>
              </label>
              <input type="text"
                     class="form-control <?= isset($errors['kode']) ? 'is-invalid' : '' ?>"
                     id="kode_kategori" name="kode_kategori"
                     value="<?= htmlspecialchars($kode) ?>"
                     maxlength="10" required>
              <?php if (isset($errors['kode'])): ?>
                <div class="invalid-feedback"><?= $errors['kode'] ?></div>
              <?php endif; ?>
              <div class="form-text">Format: KAT-angka (4–10 karakter). Kode bersifat unik.</div>
            </div>

            <!-- Nama Kategori -->
            <div class="mb-3">
              <label for="nama_kategori" class="form-label">
                Nama Kategori <span class="text-danger">*</span>
              </label>
              <input type="text"
                     class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>"
                     id="nama_kategori" name="nama_kategori"
                     value="<?= htmlspecialchars($nama) ?>"
                     maxlength="50" required>
              <?php if (isset($errors['nama'])): ?>
                <div class="invalid-feedback"><?= $errors['nama'] ?></div>
              <?php endif; ?>
              <div class="d-flex justify-content-between">
                <div class="form-text">Minimal 3, maksimal 50 karakter.</div>
                <span class="char-counter" id="namaCounter">0/50</span>
              </div>
            </div>

            <!-- Deskripsi -->
            <div class="mb-3">
              <label for="deskripsi" class="form-label">Deskripsi</label>
              <textarea class="form-control <?= isset($errors['deskripsi']) ? 'is-invalid' : '' ?>"
                        id="deskripsi" name="deskripsi" rows="3"
                        maxlength="200"><?= htmlspecialchars($deskripsi) ?></textarea>
              <?php if (isset($errors['deskripsi'])): ?>
                <div class="invalid-feedback"><?= $errors['deskripsi'] ?></div>
              <?php endif; ?>
              <div class="d-flex justify-content-between">
                <div class="form-text">Opsional, maksimal 200 karakter.</div>
                <span class="char-counter" id="deskCounter">0/200</span>
              </div>
            </div>

            <!-- Status -->
            <div class="mb-4">
              <label class="form-label">Status <span class="text-danger">*</span></label>
              <?php if (isset($errors['status'])): ?>
                <div class="text-danger small mb-1"><?= $errors['status'] ?></div>
              <?php endif; ?>
              <div class="d-flex gap-4">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="status" id="statusAktif" value="Aktif"
                         <?= $status === 'Aktif' ? 'checked' : '' ?>>
                  <label class="form-check-label" for="statusAktif">
                    <span class="badge bg-success">Aktif</span>
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="status" id="statusNonaktif" value="Nonaktif"
                         <?= $status === 'Nonaktif' ? 'checked' : '' ?>>
                  <label class="form-check-label" for="statusNonaktif">
                    <span class="badge bg-danger">Nonaktif</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Info created_at -->
            <div class="mb-3 p-3 bg-light rounded small text-muted">
              <i class="bi bi-clock me-1"></i>
              Dibuat pada: <strong><?= date('d M Y, H:i', strtotime($data['created_at'])) ?></strong>
            </div>

            <!-- Tombol Aksi -->
            <div class="d-flex gap-2 border-top pt-3">
              <button type="submit" class="btn btn-warning px-4">
                <i class="bi bi-save me-1"></i>Perbarui Data
              </button>
              <a href="index.php" class="btn btn-secondary px-3">
                <i class="bi bi-arrow-left me-1"></i>Kembali
              </a>
            </div>

          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Counter nama_kategori
  const namaInput   = document.getElementById('nama_kategori');
  const namaCounter = document.getElementById('namaCounter');
  function updateNama() {
    namaCounter.textContent = namaInput.value.length + '/50';
    namaCounter.style.color = namaInput.value.length > 45 ? '#dc3545' : '#6c757d';
  }
  namaInput.addEventListener('input', updateNama);
  updateNama();

  // Counter deskripsi
  const deskInput   = document.getElementById('deskripsi');
  const deskCounter = document.getElementById('deskCounter');
  function updateDesk() {
    deskCounter.textContent = deskInput.value.length + '/200';
    deskCounter.style.color = deskInput.value.length > 180 ? '#dc3545' : '#6c757d';
  }
  deskInput.addEventListener('input', updateDesk);
  updateDesk();

  // Auto-uppercase kode
  document.getElementById('kode_kategori').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
  });
</script>
</body>
</html>
<?php $conn->close(); ?>
