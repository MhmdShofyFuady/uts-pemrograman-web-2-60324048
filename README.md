[README.md](https://github.com/user-attachments/files/27382048/README.md)
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

### Fitur Unggulan
- Validasi server-side menyeluruh (format kode, panjang karakter, duplikasi, dll.)
- Semua query menggunakan **Prepared Statement** (aman dari SQL Injection)
- Flash message session untuk feedback sukses/error
- Character counter real-time pada form input
- Badge berwarna untuk status (Aktif/Nonaktif)
- UI responsif dengan Bootstrap 5 dan Bootstrap Icons

---

## Cara Instalasi & Menjalankan

### Prasyarat
- XAMPP / Laragon / WAMP (PHP 7.4+ & MySQL 5.7+)
- Browser modern (Chrome, Firefox, Edge)

### Langkah-langkah

1. **Clone repository** ke folder `htdocs` (XAMPP) atau `www` (WAMP):
   ```bash
   git clone https://github.com/USERNAME/uts-pemrograman-web-2-NIM.git
   # Letakkan di: C:/xampp/htdocs/uts_NIM/
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
   define('DB_NAME', 'uts_perpustakaan_NIM'); // Ganti NIM
   ```

4. **Jalankan aplikasi** di browser:
   ```
   http://localhost/uts_NIM/index.php
   ```

---

## Struktur Folder

```
uts_NIM/
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

## Stack Teknologi

| Teknologi | Versi |
|---|---|
| PHP | 7.4+ |
| MySQL | 5.7+ |
| Bootstrap | 5.3.0 |
| Bootstrap Icons | 1.10.5 |

---

## Link Repository GitHub

```
https://github.com/USERNAME/uts-pemrograman-web-2-NIM
```
*(Ganti USERNAME dan NIM sesuai akun dan NIM Anda)*
