# Frontend Web — Platform Repositori Lentera

Direktori ini memuat antarmuka pengguna berbasis peramban web (*web client*) untuk etalase publik, portal penulis, dasbor kurasi, dan panel administrasi.

---

## 1. Tujuan
- Menyediakan antarmuka visual yang responsif, intuitif, dan nyaman digunakan pada perangkat desktop, tablet, serta smartphone.
- Memfasilitasi publik dalam mencari, menyaring, membaca abstrak, dan mengunduh karya ilmiah.
- Menyediakan formulir interaktif bagi penulis untuk mengunggah naskah dan kurator untuk menelaah karya ilmiah.

---

## 2. Struktur Modul Frontend Web

```
frontend/web/
├── .htaccess            → Aturan rewrite permintaan ke indexs.php
├── indexs.php           → Entry point dan halaman beranda repositori etalase
├── csss/
│   └── styles.css       → Sistem desain CSS kustom, tipografi, dan tata letak responsif
├── jss/
│   └── apps.js          → Logika interaksi sisi klien, salin sitasi, dan validasi form
└── viewss/
    ├── admins/          → Panel manajemen Super Admin (kelola user, prodi, log)
    ├── auths/           → Halaman login, registrasi mandiri, dan logout
    ├── facultiess/      → Portal penjelajahan naskah per fakultas
    ├── reviews/         → Dasbor telaah editorial kurator
    ├── searchs/         → Halaman katalog pencarian dengan multi-filter
    ├── statisticss/     → Dasbor analitik visualisasi Chart.js
    └── workss/          → Pengelolaan naskah (unggah baru, detail, revisi, unduh, pembayaran)
```

---

## 3. Cara Menjalankan Frontend Web

1. **Prasyarat**:
   - Web server Apache lokal aktif dengan `mod_rewrite` menyala.
   - Layanan backend dan MySQL telah berjalan.
2. **Akses Browser**:
   - Buka peramban favorit Anda dan kunjungi alamat URL lokal:
     ```
     http://localhost/lentera/
     ```
   - Halaman etalase beranda akan langsung menampilkan karya-karya terbaru dan statistik koleksi.

---

## 4. Hasil & Fitur Antarmuka Web

- ✅ **Beranda Etalase**: Menampilkan kartu karya ilmiah terbit terbaru, sorotan fakultas, dan metrik repositori.
- ✅ **Pencarian Terpadu**: Fitur penelusuran naskah berdasarkan judul, nama penulis, kata kunci, serta filter per fakultas.
- ✅ **Halaman Detail & Sitasi Otomatis**: Menampilkan abstrak lengkap, metadata pembimbing, tombol unduh PDF aman, dan generator sitasi (APA, IEEE, Harvard, BibTeX).
- ✅ **Dasbor Penulis & Kurator**: Ruang kerja khusus peran untuk siklus pengajuan karya dan telaah naskah.
- ✅ **Desain Responsif**: Menggunakan media query CSS modern untuk tampilan yang rapi di layar ponsel, tablet, dan desktop tanpa overflow horizontal.
