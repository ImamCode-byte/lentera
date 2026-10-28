# Backend — Platform Repositori Lentera

Direktori ini memuat seluruh logika sisi server (*server-side*), arsitektur model-view-controller (MVC) native, koneksi basis data PDO, sistem keamanan, dan antarmuka pemrograman aplikasi (REST API).

---

## 1. Tujuan
- Menyediakan lapisan layanan data dan proses bisnis terpusat untuk aplikasi web dan mobile client.
- Mengelola autentikasi berbasis peran (Author, Curator, Admin), siklus hidup publikasi karya ilmiah, dan pencatatan audit unduhan.
- Menjamin keamanan transaksi data dari ancaman umum seperti SQL Injection, Cross-Site Scripting (XSS), dan Cross-Site Request Forgery (CSRF).

---

## 2. Struktur Modul Backend

```
backend/
├── configs/
│   ├── apps.php              → Konfigurasi URL dasar, zona waktu, direktori upload
│   ├── constantss.php        → Definisi konstanta role, status karya, dan batasan berkas
│   └── databases.php         → PDO Singleton dengan auto-healing inisialisasi basis data
├── apps/
│   ├── controllerss/         → Pengendali logika autentikasi, naskah, dan kurasi
│   ├── helperss/             → Validasi input, manajemen sesi, upload PDF, CSRF
│   ├── middlewaress/         → Pengecekan otorisasi peran pengguna
│   └── modelss/              → Abstraksi query data menggunakan prepared statements
└── databases/
    ├── migrationss/          → 14 berkas SQL DDL skema tabel berurutan
    ├── seederss/             → Data seeder awal fakultas, program studi, dan akun admin
    └── schema_and_seeds.sql  → Berkas master eksekusi auto-healing
```

---

## 3. Cara Menjalankan Backend

1. **Persyaratan Lingkungan**:
   - PHP versi 8.1 atau lebih tinggi dengan ekstensi `pdo_mysql`, `mbstring`, `fileinfo`, dan `openssl`.
   - MySQL versi 8.0+ aktif pada port default 3306.
   - Apache HTTP Server dengan modul `mod_rewrite` aktif.

2. **Konfigurasi Lingkungan**:
   - Salin berkas `.env.example` menjadi `.env` di folder `backend/`:
     ```bash
     copy .env.example .env
     ```
   - Sesuaikan kredensial basis data Anda (host, port, user, password, dbname).

3. **Inisialisasi Database**:
   - Pastikan service MySQL XAMPP telah menyala.
   - Sistem akan otomatis mendeteksi ketiadaan basis data `lentera_db` dan membuat skema beserta seeder awal melalui mekanisme *auto-healing*.
   - Untuk menjalankan migrasi secara manual:
     ```bash
     mysql -u root -p lentera_db < databases/schema_and_seeds.sql
     mysql -u root -p lentera_db < databases/migrationss/014_add_waiting_payment_enum.sql
     ```

---

## 4. Hasil & Fitur yang Disediakan

- ✅ **Keamanan Berlapis**: Seluruh query menggunakan PDO parameterized statements (`EMULATE_PREPARES = false`), form mutasi data dilindungi CSRF token, dan password dienkripsi dengan algoritma bcrypt.
- ✅ **Manajemen Siklus Karya**: Mendukung status draft, submitted, in_review, revision, waiting_payment, published, dan rejected.
- ✅ **Audit Log Unduhan**: Setiap unduhan PDF mencatat identitas pengunduh, IP address, waktu, dan penambahan counter unduhan secara otomatis.
- ✅ **Pencegahan Akses Langsung**: Folder upload dilindungi konfigurasi server sehingga berkas tidak dapat diakses langsung tanpa melalui pemeriksaan otorisasi di controller.
- 📋 **REST API JSON**: Endpoint terstruktur untuk konsumsi aplikasi mobile native (Fase 4).
