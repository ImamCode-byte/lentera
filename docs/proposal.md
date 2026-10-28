# Proposal Proyek Akhir — LENTERA

## Informasi Proyek
- **Mata Kuliah:** Pemrograman Berbasis Platform
- **Topik Proyek:** Repositori & Etalase Karya Ilmiah Terpadu Akademik
- **Judul Proyek:** LENTERA (*Laman Etalase Karya Tulis Ilmiah Terpadu Akademik*)
- **Kelompok:** 01
- **Program Studi / Fakultas:** [ISI_PROGRAM_STUDI] / [ISI_FAKULTAS]
- **Perguruan Tinggi:** Universitas Madura (UNIRA)

---

## 1. Latar Belakang & Rumusan Masalah

### 1.1 Latar Belakang
Setiap tahunnya, civitas akademika di lingkungan perguruan tinggi menghasilkan ratusan hingga ribuan karya intelektual yang bernilai tinggi, meliputi skripsi, tesis, jurnal penelitian, laporan magang/kerja praktik, dan laporan pengabdian masyarakat. Seluruh naskah tersebut merepresentasikan memori intelektual dan rekam jejak riset akademik kampus.

Namun dalam praktiknya, pengelolaan karya ilmiah masih menghadapi berbagai kendala klasik:
1. **Penyimpanan Terfragmentasi**: Karya sering kali hanya tertimbun secara fisik di lemari arsip program studi, tersimpan di flashdisk mahasiswa yang telah lulus, atau tercecer di penyimpanan cloud pribadi masing-masing dosen pembimbing.
2. **Keterbatasan Akses Referensi**: Mahasiswa tingkat akhir atau peneliti pemula kesulitan menelusuri karya-karya terdahulu yang relevan sebagai rujukan literatur, sehingga terjadi duplikasi topik penelitian yang tidak disengaja.
3. **Kendala Pelaporan Akreditasi**: Ketika periode evaluasi borang akreditasi program studi tiba, pengelola program studi kelimpungan mengumpulkan data fisik serta bukti keterbacaan publikasi dalam waktu singkat.
4. **Ketiadaan Sistem Kurasi Terstandar**: Pengunggahan karya secara mandiri tanpa saringan telaah mutu berisiko mempublikasikan naskah yang belum memenuhi standar sitasi atau integritas karya ilmiah.

### 1.2 Rumusan Masalah
1. Bagaimana merancang repositori digital terpadu yang memfasilitasi pengunggahan karya ilmiah dengan metadata terstruktur dan penyimpanan aman?
2. Bagaimana membangun mekanisme kurasi dan penelaahan bertingkat (*peer editorial review*) sebelum suatu naskah disetujui terbit ke publik?
3. Bagaimana menyediakan keterbukaan akses pencarian multi-kriteria (berdasarkan judul, kata kunci, abstrak, fakultas, dan program studi) yang dapat diakses secara cepat di peramban web dan aplikasi mobile native?

---

## 2. Pengguna Sasaran (Target Audience)

Sistem LENTERA berpusat pada tiga aktor utama (*The Triad Actors*):

1. **Penulis (Author — Mahasiswa & Dosen)**
   - Sasaran: Mahasiswa tingkat sarjana/magister serta dosen peneliti.
   - Kebutuhan: Mengunggah naskah dan berkas PDF, memantau riwayat status naskah secara transparan, menerima umpan balik/catatan perbaikan dari kurator, dan mengunggah dokumen revisi berversi.

2. **Kurator (Curator — Dewan Editor & Tim Mutu Program Studi)**
   - Sasaran: Dosen penelaah, pengelola jurnal internal, atau tim jaminan mutu akademik.
   - Kebutuhan: Dasbor antrean kurasi naskah masuk, fasilitas membaca naskah, pemberian catatan telaah revisi, serta wewenang menerbitkan (*approve*) atau menolak (*reject*) naskah.

3. **Publik (Guest / Readers)**
   - Sasaran: Mahasiswa, dosen pembimbing, peneliti eksternal, dan masyarakat umum.
   - Kebutuhan: Melakukan penelusuran katalog karya secara bebas hambatan tanpa kewajiban login, membaca abstrak, menyalin format sitasi otomatis (APA, IEEE, Harvard), dan mengunduh berkas lengkap (PDF).

4. **Super Admin (Pengelola Sistem IT)**
   - Sasaran: Operator repositori atau staf IT kampus.
   - Kebutuhan: Manajemen master data (fakultas, program studi, jenis karya), pemantauan log aktivitas unduhan, dan pengawasan metrik kinerja sistem.

---

## 3. Ide Solusi & Keunggulan Inovasi

### 3.1 Ide Solusi
Membangun platform **LENTERA** dengan arsitektur multiplatform:
- **Backend Berbasis PHP Native MVC**: Ringan, cepat, aman dengan PDO prepared statements dan CSRF protection, serta dilengkapi API endpoints berformat JSON standar.
- **Frontend Web Responsif**: Menggunakan CSS kustom dan Vanilla JS yang ringan tanpa bloatware, responsif di berbagai resolusi layar ponsel hingga monitor desktop.
- **Frontend Mobile Native (Flutter)**: Aplikasi Android & iOS untuk mempermudah civitas akademika mencari, membaca abstrak, dan mengunduh referensi secara mobile.

### 3.2 Keunggulan Utama
- **Siklus Telaah Berversi (Versioned Revision Cycle)**: Revisi naskah tersimpan secara berurutan ($v_1, v_2, \dots$) sehingga riwayat perbaikan terdokumentasi rapi.
- **Generator Sitasi Otomatis**: Memudahkan periset mengutip karya ilmiah dalam format standar akademik dengan satu klik.
- **Pencatatan Audit Log Unduhan Realtime**: Setiap unduhan tercatat alamat IP, identitas pengguna, dan waktunya, memberikan analitik akurat bagi keperluan borang akreditasi.
- **Penyimpanan Berkas Terproteksi**: Direktori berkas PDF naskah dilindungi secara ketat di tingkat web server; unduhan hanya dapat melalui controller resmi yang memverifikasi status publikasi.

---

## 4. Alasan Pemilihan Topik

1. **Relevansi Nyata dengan Kebutuhan Kampus**: Topik repositori karya ilmiah ini menjawab masalah nyata di Universitas Madura (UNIRA) dalam mendokumentasikan memori intelektual kampus.
2. **Kesesuaian Capaian Pembelajaran Mata Kuliah**: Mengintegrasikan konsep pemrograman server-side (PHP), client-side interaktif (JavaScript), perancangan basis data relasional (MySQL), keamanan aplikasi web, serta pengembangan multiplatform mobile (Flutter).
3. **Dampak Berkelanjutan**: Aplikasi ini tidak hanya berhenti sebagai tugas kuliah, namun siap diimplementasikan secara konkret sebagai aset digital akademik perguruan tinggi.

---

> [!NOTE]
> Bagian yang ditandai dengan `[ISI_...]` dapat dilengkapi saat finalisasi dokumen pengumpulan tugas kelompok.
