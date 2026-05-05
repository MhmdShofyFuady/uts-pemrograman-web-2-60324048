[README.md](https://github.com/user-attachments/files/27382085/README.md)
# UTS Pemrograman Web 2 — Sistem Manajemen Kategori Buku

## Identitas

| | |
|---|---|
| **Nama** | Muhammad Shofy Fuady |
| **NIM** | 60324048 |
| **Mata Kuliah** | Pemrograman Web 2 |
| **Tugas** | UTS — Sistem Manajemen Kategori Buku |

---

## Deskripsi Aplikasi

Aplikasi web sederhana berbasis **PHP & MySQL** untuk mengelola data Kategori Buku di perpustakaan. Aplikasi ini memiliki fitur **CRUD lengkap**:

- ✅ **Create** — Tambah kategori baru dengan validasi lengkap
- ✅ **Read** — Tampilkan semua kategori dalam tabel Bootstrap yang responsif
- ✅ **Update** — Edit data kategori yang sudah ada
- ✅ **Delete** — Hapus kategori dengan konfirmasi JavaScript


---

## Cara Instalasi & Menjalankan

### Prasyarat
- XAMPP / Laragon / WAMP (PHP 7.4+ & MySQL 5.7+)
- Browser modern (Chrome, Firefox, Edge)

### Langkah-langkah

1. **Clone repository** ke folder `htdocs` (XAMPP) atau `www` (WAMP):
   ```bash
   git clone https://github.com/MhmdShofyFuady/uts-pemrograman-web-2-60324048.git
   # Letakkan di: /Applications/XAMPP/xamppfiles/htdocs/
   ```

2. **Import database** via phpMyAdmin atau command line:
   ```bash
   # Via phpMyAdmin:
   # Buka localhost/phpmyadmin → Import → pilih file database/database_backup.sql

   # Via command line:
   mysql -u root -p < database/database_backup.sql
   ```

3. **Konfigurasi koneksi** — buka `config/database.php` dan sesuaikan:
   ```php
   define('DB_NAME', 'uts_perpustakaan_60324048');
   ```

4. **Jalankan aplikasi** di browser:
   ```
   http://localhost/uts_60324048/index.php
   ```

---

## Struktur Folder

```
uts_60324048/
├── config/
│   └── database.php       # Konfigurasi & koneksi database + helper functions
├── database/
│   └── database_backup.sql # Export database (struktur + sample data)
├── index.php              # READ — Daftar semua kategori
├── create.php             # CREATE — Form tambah kategori baru
├── edit.php               # UPDATE — Form edit kategori
├── delete.php             # DELETE — Proses hapus kategori
└── README.md              # Dokumentasi ini
```

---

## Link Repository GitHub

```
https://github.com/MhmdShofyFuady/uts-pemrograman-web-2-60324048.git
```
