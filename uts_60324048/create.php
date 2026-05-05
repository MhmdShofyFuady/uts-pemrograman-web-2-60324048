<?php
session_start();
require_once 'config/database.php';

$errors   = [];
$kode     = '';
$nama     = '';
$deskripsi = '';
$status   = 'Aktif';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $kode      = trim($_POST['kode_kategori'] ?? '');
    $nama      = trim($_POST['nama_kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status    = trim($_POST['status'] ?? '');

    if ($kode === '') {
        $errors['kode'] = 'Kode Kategori wajib diisi.';
    } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
        $errors['kode'] = 'Kode Kategori harus antara 4–10 karakter.';
    } elseif (!preg_match('/^KAT-\d+$/i', $kode)) {
        $errors['kode'] = 'Format kode harus diawali "KAT-" diikuti angka. Contoh: KAT-001';
    } else {
        $stmtCek = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori = ?");
        $stmtCek->bind_param("s", $kode);
        $stmtCek->execute();
        $stmtCek->store_result();
        if ($stmtCek->num_rows > 0) {
            $errors['kode'] = 'Kode Kategori sudah digunakan. Gunakan kode yang berbeda.';
        }
        $stmtCek->close();
    }

    if ($nama === '') {
        $errors['nama'] = 'Nama Kategori wajib diisi.';
    } elseif (mb_strlen($nama) < 3) {
        $errors['nama'] = 'Nama Kategori minimal 3 karakter.';
    } elseif (mb_strlen($nama) > 50) {
        $errors['nama'] = 'Nama Kategori maksimal 50 karakter.';
    }

    if ($deskripsi !== '' && mb_strlen($deskripsi) > 200) {
        $errors['deskripsi'] = 'Deskripsi maksimal 200 karakter.';
    }

    if (!in_array($status, ['Aktif', 'Nonaktif'])) {
        $errors['status'] = 'Status tidak valid.';
    }
    
    if (empty($errors)) {
        $stmtInsert = $conn->prepare(
            "INSERT INTO kategori (kode_kategori, nama_kategori, deskripsi, status) VALUES (?, ?, ?, ?)"
        );
        $deskNull = $deskripsi === '' ? null : $deskripsi;
        $stmtInsert->bind_param("ssss", $kode, $nama, $deskNull, $status);

        if ($stmtInsert->execute()) {
            setFlash('success', "Kategori <strong>{$nama}</strong> berhasil ditambahkan!");
            header("Location: index.php");
            exit;
        } else {
            $errors['global'] = 'Gagal menyimpan data. Silakan coba lagi.';
        }
        $stmtInsert->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Kategori — UTS Perpustakaan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    body { background-color: #f0f4f8; }
    .card { border: none; box-shadow: 0 2px 12px rgba(0,0,0,.08); border-radius: 12px; }
    .card-header { background: linear-gradient(135deg, #198754, #146c43); color: #fff; border-radius: 12px 12px 0 0 !important; }
    .form-label { font-weight: 600; font-size: .88rem; }
    .form-text { font-size: .78rem; }
    .char-counter { font-size: .75rem; color: #6c757d; }
    .is-invalid ~ .invalid-feedback { display: block; }
  </style>
</head>
<body>

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

      <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Daftar Kategori</a></li>
          <li class="breadcrumb-item active">Tambah Kategori</li>
        </ol>
      </nav>

      <div class="card">
        <div class="card-header py-3">
          <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Kategori Baru</h5>
        </div>
        <div class="card-body p-4">

          <?php if (!empty($errors['global'])): ?>
          <div class="alert alert-danger d-flex gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?= $errors['global'] ?>
          </div>
          <?php endif; ?>

          <?php if (count($errors) > 1 || (count($errors) === 1 && !isset($errors['global']))): ?>
          <div class="alert alert-warning d-flex gap-2 align-items-start">
            <i class="bi bi-exclamation-circle-fill mt-1"></i>
            <div>Terdapat <strong><?= count($errors) ?> kesalahan</strong> pada form. Silakan periksa kembali.</div>
          </div>
          <?php endif; ?>

          <form method="POST" novalidate>

            <div class="mb-3">
              <label for="kode_kategori" class="form-label">
                Kode Kategori <span class="text-danger">*</span>
              </label>
              <input type="text"
                     class="form-control <?= isset($errors['kode']) ? 'is-invalid' : '' ?>"
                     id="kode_kategori" name="kode_kategori"
                     value="<?= htmlspecialchars($kode) ?>"
                     placeholder="Contoh: KAT-006"
                     maxlength="10" required>
              <?php if (isset($errors['kode'])): ?>
                <div class="invalid-feedback"><?= $errors['kode'] ?></div>
              <?php endif; ?>
              <div class="form-text">Format: KAT- diikuti angka (4–10 karakter). Kode bersifat unik.</div>
            </div>

            <div class="mb-3">
              <label for="nama_kategori" class="form-label">
                Nama Kategori <span class="text-danger">*</span>
              </label>
              <input type="text"
                     class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>"
                     id="nama_kategori" name="nama_kategori"
                     value="<?= htmlspecialchars($nama) ?>"
                     placeholder="Contoh: Pemrograman Web"
                     maxlength="50" required>
              <?php if (isset($errors['nama'])): ?>
                <div class="invalid-feedback"><?= $errors['nama'] ?></div>
              <?php endif; ?>
              <div class="d-flex justify-content-between">
                <div class="form-text">Minimal 3, maksimal 50 karakter.</div>
                <span class="char-counter" id="namaCounter">0/50</span>
              </div>
            </div>

            <div class="mb-3">
              <label for="deskripsi" class="form-label">Deskripsi</label>
              <textarea class="form-control <?= isset($errors['deskripsi']) ? 'is-invalid' : '' ?>"
                        id="deskripsi" name="deskripsi" rows="3"
                        maxlength="200"
                        placeholder="Keterangan singkat tentang kategori ini (opsional)"><?= htmlspecialchars($deskripsi) ?></textarea>
              <?php if (isset($errors['deskripsi'])): ?>
                <div class="invalid-feedback"><?= $errors['deskripsi'] ?></div>
              <?php endif; ?>
              <div class="d-flex justify-content-between">
                <div class="form-text">Opsional, maksimal 200 karakter.</div>
                <span class="char-counter" id="deskCounter">0/200</span>
              </div>
            </div>

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

            <div class="d-flex gap-2 border-top pt-3">
              <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-save me-1"></i>Simpan Kategori
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

  const namaInput   = document.getElementById('nama_kategori');
  const namaCounter = document.getElementById('namaCounter');
  function updateNamaCounter() {
    namaCounter.textContent = namaInput.value.length + '/50';
    namaCounter.style.color = namaInput.value.length > 45 ? '#dc3545' : '#6c757d';
  }
  namaInput.addEventListener('input', updateNamaCounter);
  updateNamaCounter();

  const deskInput   = document.getElementById('deskripsi');
  const deskCounter = document.getElementById('deskCounter');
  function updateDeskCounter() {
    deskCounter.textContent = deskInput.value.length + '/200';
    deskCounter.style.color = deskInput.value.length > 180 ? '#dc3545' : '#6c757d';
  }
  deskInput.addEventListener('input', updateDeskCounter);
  updateDeskCounter();

  document.getElementById('kode_kategori').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
  });
</script>
</body>
</html>
<?php $conn->close(); ?>
