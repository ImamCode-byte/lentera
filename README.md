# 🔥 LENTERA — Laman Etalase Karya Tulis Ilmiah Terpadu Akademik

> **"Menerangi karya, menginspirasi generasi."**  
> Repositori digital terpadu untuk penerbitan, penelaahan (kurasi), dan keterbukaan akses karya tulis ilmiah civitas akademika **Universitas Madura (UNIRA)**.

---

## 👥 Identitas Kelompok 01

**Mata Kuliah:** Pemrograman Berbasis Platform  
**Nama Repository:** `pemrograman-berbasis-platform-kelompok-01-lentera`  
**Laporan Proyek:** `docs/Laporan-Kelompok-01-Lentera.docx`

| No | Nama Mahasiswa | NIM | Akun GitHub | Peran Utama |
|:--:|---|:--:|:--:|---|
| 1 | **Imam** | 2024520030 | `ISI_GITHUB_IMAM` | **Ketua Kelompok**, Arsitektur Backend, Keamanan, Integrasi REST API |
| 2 | **Alvin Hidayat Putra Pratam** | 2024520050 | `ISI_GITHUB_ALVIN` | **Frontend Web Engineer**, Desain UI/UX Responsif Desktop & Mobile |
| 3 | **Moh. Gilank Alamsyah** | 2024520052 | `ISI_GITHUB_GILANK` | **Mobile Engineer**, Pengembangan Aplikasi Native Android & iOS (Flutter) |
| 4 | **Abdul Halim** | 2024520059 | `ISI_GITHUB_HALIM` | **Dokumentasi & QA**, Manajemen Repositori, Pengujian Fungsional & Kuesioner Pengguna |

---

## 📖 Ringkasan Proyek

Setiap tahun civitas akademika menghasilkan ratusan karya berharga: skripsi, tesis, jurnal, laporan penelitian, dan laporan kerja praktik. Sebelum ada sistem terpadu, karya-karya tersebut seringkali menumpuk di lemari arsip fisik atau tersimpan di ruang penyimpanan pribadi yang sulit dijangkau publik maupun adik tingkat.

**Lentera** hadir menjawab permasalahan tersebut sebagai repositori etalase digital akademik terpadu yang memfasilitasi:
- 📝 **Penulis (Author)**: Mahasiswa dan dosen mengunggah naskah karya ilmiah lengkap beserta metadata, dosen pembimbing, dan berkas PDF.
- 🔍 **Kurator (Curator)**: Dewan kurasi/editor memeriksa kelayakan isi, integritas akademik, dan memberikan keputusan telaah sebelum naskah dapat diterbitkan.
- 🌐 **Publik (Guest)**: Civitas akademika maupun masyarakat umum dapat mencari, menyaring per fakultas/program studi, membaca abstrak, dan mengunduh berkas resmi.

---

## 🛠️ Stack Teknologi

| Komponen | Teknologi | Keterangan |
|---|---|---|
| **Backend** | PHP 8.1+ (Native MVC) | Arsitektur MVC terstruktur tanpa ketergantungan framework berat |
| **Basis Data** | MySQL 8.0+ | Engine InnoDB, Prepared Statements (PDO Anti-SQL Injection) |
| **Web Frontend** | HTML5, CSS3 Custom Tokens, Vanilla JavaScript | Responsif desktop, tablet, dan smartphone |
| **Mobile App** | Flutter (Dart SDK) | Native multiplatform Android dan iOS |
| **Web Server** | Apache (XAMPP Environment) | Modul `mod_rewrite` aktif via `.htaccess` |
| **Grafik & Visualisasi** | Chart.js | Visualisasi statistik karya per fakultas & tren publikasi |

---

## 📁 Struktur Repositori (Standar Dosen)

```
pemrograman-berbasis-platform-kelompok-01-lentera/
├── .github/
│   ├── ISSUE_TEMPLATE/
│   │   └── kendala.md                 → Format pelaporan kendala (GitHub Issue)
│   └── pull_request_template.md       → Standar pemeriksaan Pull Request
├── .gitignore                         → Aturan pengabaian berkas sensitif & build
├── .env.example                       → Template variabel lingkungan aman
├── CONTRIBUTING.md                    → Alur kerja Git, commit, branch, dan review
├── README.md                          → Dokumentasi utama proyek kelompok
├── docs/
│   ├── Laporan-Kelompok-01-Lentera.docx → Dokumen laporan akhir proyek
│   ├── tautan-video-pengujian.md      → Tautan video pengujian & QA
│   ├── tugas-kelompok-1-kelompok.md   → Data kelompok dan pembagian tugas
│   ├── proposal.md                    → Latar belakang, sasaran, dan solusi
│   ├── backlog.md                     → Manajemen tugas & status pengerjaan
│   └── prds.md                        → Product Requirement Document teknis
├── backend/
│   ├── README.md                      → Panduan instalasi & arsitektur backend
│   ├── .env.example                   → Template konfigurasi lokal backend
│   ├── configs/                       → Konfigurasi aplikasi, database & konstanta
│   ├── apps/
│   │   ├── controllerss/              → Pengendali alur autentikasi, karya & review
│   │   ├── modelss/                   → Model data PDO prepared statements
│   │   ├── helperss/                  → CSRF, session, validasi input & upload PDF
│   │   └── middlewaress/              → Otorisasi peran & proteksi route
│   └── databases/
│       ├── migrationss/               → 14 berkas migrasi SQL DDL berurutan
│       └── seederss/                  → Berkas seeder data awal akademik
├── frontend/
│   ├── web/
│   │   ├── README.md                  → Panduan antarmuka web
│   │   ├── indexs.php                 → Halaman muka etalase repositori publik
│   │   ├── csss/styles.css            → Desain antarmuka responsif
│   │   ├── jss/apps.js                → Interaksi client-side & generator sitasi
│   │   └── viewss/                    → Antarmuka penulis, kurator, admin, statistik
│   └── mobile/
│       ├── README.md                  → Panduan aplikasi mobile Flutter
│       └── lib/                       → Kode sumber antarmuka mobile native
└── uploads/
    ├── payment/                       → Direktori terlindungi (bukti transaksi)
    └── works/                         → Direktori naskah PDF (hanya via downloads.php)
```

---

## ⚙️ Cara Menjalankan Aplikasi Web

### 1. Prasyarat Sistem
- **XAMPP** terpasang dengan PHP 8.1+ dan MySQL 8.0+.
- Web server Apache dengan modul `mod_rewrite` aktif.
- Peramban web modern (Google Chrome, Mozilla Firefox, Microsoft Edge, atau Safari).

### 2. Langkah Pemasangan
1. Klon repositori ke direktori web server lokal:
   ```bash
   cd C:\xampp\htdocs
   git clone https://github.com/ISI_PEMILIK/pemrograman-berbasis-platform-kelompok-01-lentera.git lentera
   cd lentera
   ```
2. Siapkan konfigurasi lingkungan:
   - Salin file `.env.example` di root atau di `backend/.env.example` menjadi `backend/.env`.
   - Sesuaikan konfigurasi basis data:
     ```env
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_NAME=lentera_db
     DB_USER=root
     DB_PASS=
     ```
3. Inisialisasi Basis Data:
   - Nyalakan modul **Apache** dan **MySQL** di XAMPP Control Panel.
   - Sistem dilengkapi mekanisme *Auto-Healing*: saat halaman web dibuka pertama kali, basis data `lentera_db` dan seluruh tabelnya akan diinisialisasi secara otomatis.
   - Alternatif: jalankan migrasi berkas SQL dari direktori `backend/databases/schema_and_seeds.sql`.
4. Buka aplikasi di peramban:
   ```
   http://localhost/lentera/
   ```

---

## 🔑 Informasi Akun & Pengujian

Demi keamanan repositori publik, kata sandi nyata tidak dicantumkan dalam berkas git.
- **Penulis (Author)**: Buat akun baru secara mandiri melalui menu **Registrasi** (`frontend/web/viewss/auths/registers.php`).
- **Super Admin & Kurator**: Akun pengelola diinisialisasi melalui seeder lokal saat instalasi pertama.
- **Publik**: Seluruh naskah terbit dapat dicari, dibaca abstraknya, dan diunduh tanpa perlu masuk ke sistem.

---

## 📜 Panduan Kontribusi & Alur Git

Setiap anggota kelompok wajib mengikuti panduan yang tertera pada [CONTRIBUTING.md](file:///d:/xampp/htdocs/lentera/CONTRIBUTING.md):
- Gunakan branch kerja terpisah (misalnya `fase-1-keamanan-imam`, `web-responsif-alvin`, `mobile-flutter-gilank`, `dokumen-halim`).
- Format pesan commit wajib: `<tipe>(<bagian>): <ringkasan perubahan>`.
- Setiap kendala dicatat transparan pada GitHub Issue menggunakan template [`.github/ISSUE_TEMPLATE/kendala.md`](file:///d:/xampp/htdocs/lentera/.github/ISSUE_TEMPLATE/kendala.md).
- Seluruh penggabungan kode melalui Pull Request wajib menggunakan checklist [`.github/pull_request_template.md`](file:///d:/xampp/htdocs/lentera/.github/pull_request_template.md) dan ditinjau melalui mekanisme review silang antaranggota.
