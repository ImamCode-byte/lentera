# 🔥 lentera

**laman etalase karya tulis ilmiah terpadu akademik**

> *menerangi karya, menginspirasi generasi*

repositori karya ilmiah digital untuk mengelola, mengkurasi, dan mempublikasikan karya tulis ilmiah secara terpusat. dibangun untuk universitas madura (unira).

---

## 📋 tentang

lentera adalah platform digital yang memungkinkan:
- **penulis** (mahasiswa/dosen) mengunggah karya ilmiah lengkap dengan metadata
- **kurator** menjaga mutu dengan sistem telaah sebelum publikasi
- **publik** mencari, membaca, dan mengunduh naskah yang telah terbit

jenis karya yang dikelola: skripsi, tesis, jurnal ilmiah, laporan kerja praktik, dan laporan penelitian.

## 🛠️ tech stack

| layer | teknologi |
|-------|-----------|
| backend | php 8.1+ (native mvc) |
| database | mysql 8.0+ (innodb, prepared statements) |
| frontend | html5, css3, vanilla javascript |
| server | apache (xampp) |
| font | plus jakarta sans (google fonts) |
| chart | chart.js (cdn) |

## 📁 struktur folder & file

```
lentera/
├── .htaccess                         → url rewrite apache root ke frontends/webs/indexs.php
├── .gitignore
├── readmes.md                        → dokumentasi repositori
├── uploads/
│   └── works/                        → penyimpanan berkas pdf aman
├── docss/
│   ├── laporan-kelompok-01-mikrokredensial.docx
│   └── tautan-video-pengujians.md
├── backends/
│   ├── .htaccess
│   ├── .env & .env.example
│   ├── configs/
│   │   ├── apps.php                  → konfigurasi aplikasi & base url
│   │   ├── constantss.php            → konstanta peran & status karya
│   │   └── databases.php             → pdo connection auto-healing
│   ├── databases/
│   │   ├── schema_and_seeds.sql      → master sql skema & data awal
│   │   ├── migrationss/              → 13 berkas ddl sql
│   │   └── seederss/                 → 5 berkas seeder unira
│   └── apps/
│       ├── controllerss/             → auth, work, review controllers
│       ├── helperss/                 → session, csrf, validation, upload, flash
│       ├── middlewaress/             → middleware autentikasi & peran
│       └── modelss/                  → user, work, review, study program, work type
└── frontends/
    ├── mobiles/                      → pengembangan masa depan
    └── webs/
        ├── .htaccess                 → directoryindex indexs.php
        ├── indexs.php                → entry point beranda etalase
        ├── csss/
        │   └── styles.css            → sistem desain responsif mobile
        ├── jss/
        │   └── apps.js               → logika interaktif client-side
        └── viewss/
            ├── auths/                → logins, registers, logouts
            ├── reviews/              → dashboard kurasi kurator
            ├── searchs/              → katalog & pencarian filter
            ├── statisticss/          → analitik & akreditasi
            └── workss/               → creates, indexs, details, revises, downloads
```

## ⚙️ instalasi & setup

### prasyarat
- xampp (php 8.1+, mysql 8.0+, apache)
- browser modern (chrome / edge / firefox)

### langkah instalasi

1. **letakkan proyek di xampp**
   ```bash
   # lokasi folder: D:\xampp\htdocs\lentera
   ```

2. **konfigurasi basis data**
   - buka xampp control panel, aktifkan **apache** dan **mysql**
   - sistem memiliki fitur *auto-healing*: pada saat pertama kali web dibuka, database `lentera_db` dan seluruh tabelnya akan diinisialisasi otomatis dari `backends/databases/schema_and_seeds.sql`.
   - atau impor manual melalui phpmyadmin: `backends/databases/schema_and_seeds.sql`

3. **akses aplikasi**
   ```
   http://localhost/lentera/
   ```

### akun default
| peran | email | password |
|-------|-------|----------|
| super admin | admin@lentera.unira.ac.id | LenteraAdmin2026! |

## 👥 lisensi & kepemilikan

project ini dibuat untuk repositori akademik terpadu universitas madura.

---

*lentera — menerangi karya, menginspirasi generasi*
