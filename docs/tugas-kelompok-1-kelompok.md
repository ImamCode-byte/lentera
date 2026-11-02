# Tugas Kelompok 1 — Kelompok 01 Lentera

## Profil Kelompok & Pembagian Peran

**Mata Kuliah:** Pemrograman Berbasis Platform  
**Topik Proyek:** Repositori Karya Ilmiah Terpadu Akademik (Lentera) — Pilihan Proyek Nomor 4 (E-Library)  
**Tautan Repositori Kelompok:** `https://github.com/ImamCode-byte/LENTERA`

| No | Nama Anggota | NIM | Email GitHub / Akun | Peran Utama | Rincian Tanggung Jawab |
|:--:|---|:--:|:--:|---|---|
| 1 | **Imam** | 2024520030 | `imamdhespachito001@gmail.com` | Ketua Kelompok, Backend Lead & Keamanan | Perancangan arsitektur MVC, database PDO prepared statements, keamanan (.gitignore, CSRF, enkripsi hash sandi, otorisasi download), perancangan REST API JSON, review PR & merge ke branch main. |
| 2 | **Alvin Hidayat Putra Pratam** | 2024520050 | `alvnhdytptr@gmail.com` | Frontend Web Engineer | Pembangunan antarmuka web responsif (desktop, tablet, smartphone), implementasi standar penamaan "Lentera", pembersihan folder views, integrasi grafik Chart.js, dokumentasi frontend/web. |
| 3 | **Moh. Gilank Alamsyah** | 2024520052 | `alamsyahgilank@gmail.com` | Mobile Native Engineer | Pengembangan aplikasi mobile Flutter (Android & iOS), penanganan offline & graceful failure, implementasi safe area, pengujian emulator Android, dokumentasi frontend/mobile. |
| 4 | **Abdul Halim** | 2024520059 | `kikybagaskara@gmail.com` | Database & QA Documentation Lead | Perancangan 14 tabel relasional InnoDB, migrasi SQL 001-017, penyusunan proposal, backlog & issue template, koordinasi uji fungsional, dan penjaminan kualitas (QA). |

---

## Mekanisme Kolaborasi Kelompok

1. **Komunikasi & Pelaporan Kendala**: Dilakukan secara transparan melalui *GitHub Issues* menggunakan template resmi `kendala.md`.
2. **Pengembangan Fitur**: Dilakukan pada branch kerja masing-masing dengan pesan commit terstruktur (`feat`, `fix`, `docs`, `test`).
3. **Pemeriksaan Kode (Code Review)**: Dilakukan review silang melingkar antaranggota sebelum penggabungan kode ke branch `main`:
   - PR Imam ditinjau oleh Alvin.
   - PR Alvin ditinjau oleh Gilank.
   - PR Gilank ditinjau oleh Halim.
   - PR Halim ditinjau oleh Imam.
