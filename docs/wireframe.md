# 2.7 Wireframe — Sistem Repositori LENTERA
### Laman Etalase Karya Tulis Ilmiah Terpadu Akademik Universitas Madura (UNIRA)

Dokumen **Wireframe** ini merupakan cetak biru tata letak antarmuka (*UI Layout Blueprint*) untuk platform **LENTERA** yang dirancang menggunakan perangkat lunak **MockFlow WireframePro** ([https://mockflow.com/](https://mockflow.com/)). Perancangan wireframe mengadopsi prinsip *Mobile-First & Responsive Web Design* dengan standar aksesibilitas WCAG 2.2 AA (target sentuh tombol minimal $44 \times 44\text{ piksel}$, rasio kontras teks minimal $4.5:1$, dan kisi tata letak berbasis sistem grid 8-titik / *8pt Grid System*).

---

### 🛠️ Informasi Alat & Proyek Desain MockFlow
- **Aplikasi Desain**: **MockFlow WireframePro** ([https://mockflow.com/](https://mockflow.com/))
- **Komponen UI Kit**: MockFlow UI Web & Mobile Native Pack (Bootstrap / Material Design)
- **Target Resolusi**:
  - **Desktop Web Viewport**: $1280 \times 800\text{ piksel}$ (Bootstrap 12-Column Grid)
  - **Tablet Viewport**: $768 \times 1024\text{ piksel}$
  - **Smartphone Mobile Viewport**: $375 \times 812\text{ piksel}$ (iPhone / Android Screen)
- **Tipografi Sistem**: *Plus Jakarta Sans* (Heading Bold, Body Regular 14–16px)

---

### A. Daftar 8 Wireframe Antarmuka Sistem LENTERA

1. **WF-01**: Wireframe Halaman Beranda & Etalase Publik (Landing Page)
2. **WF-02**: Wireframe Halaman Pencarian Multi-Filter & Hasil Temuan
3. **WF-03**: Wireframe Halaman Detail Naskah & Generator Sitasi 1-Klik
4. **WF-04**: Wireframe Dasbor Karya Penulis & Formulir Pengajuan Naskah Baru
5. **WF-05**: Wireframe Ruang Kerja Antrean Kurasi & Modal Keputusan OJS
6. **WF-06**: Wireframe Portal Pembayaran APC & Unggah Struk Bukti Transfer
7. **WF-07**: Wireframe Panel Super Admin & Verifikasi Pembayaran Publikasi
8. **WF-08**: Wireframe Aplikasi Mobile Native (Flutter - Android / iOS)

---

### B. Spesifikasi & Cetak Biru Tata Letak Wireframe (MockFlow)

---

#### 1. Gambar 8.1 — Wireframe: Halaman Beranda & Etalase Publik (WF-01)
*Halaman muka repositori yang menyambut civitas akademika dengan pencarian cepat, ringkasan metrik koleksi, dan daftar karya terbitan terbaru.*

```
+----------------------------------------------------------------------------------------------------+
|  [Logo UNIRA] LENTERA UNIRA       [Beranda]  [Koleksi Fakultas]  [Statistik]    [Masuk] [Daftar]   |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|                                       🏛️ REPOSITORI DIGITAL                                         |
|                       Laman Etalase Karya Tulis Ilmiah Civitas Akademika                           |
|                       Universitas Madura — Menerangi Karya, Menginspirasi Generasi                 |
|                                                                                                    |
|       +----------------------------------------------------------------------+  [ Cari Naskah ]    |
|       | 🔍 Masukkan kata kunci judul, topik skripsi, nama penulis...        |                     |
|       +----------------------------------------------------------------------+                     |
|                                                                                                    |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|    +------------------------+   +------------------------+   +------------------------+           |
|    |   📚 1,248 Judul       |   |   📥 18,420 Unduhan    |   |   👥 842 Penulis       |           |
|    |   Total Naskah Terbit  |   |   Akses Publik Aman    |   |   Civitas UNIRA        |           |
|    +------------------------+   +------------------------+   +------------------------+           |
|                                                                                                    |
|  --- KARYA ILMIAH TERBARU -------------------------------------------------------- [Lihat Semua] --|
|                                                                                                    |
|  +-------------------------------------+   +-------------------------------------+                |
|  | [SKRIPSI] 2026 • Teknik Informatika |   | [JURNAL] 2026 • Ilmu Hukum          |                |
|  | Optimasi Algoritma Kriptografi AES |   | Analisis Yuridis Hak Cipta Digital  |                |
|  | Penulis: Imam (NIM: 2024520030)     |   | Penulis: Siti Aminah                |                |
|  | Abstrak: Penelitian ini mengkaji... |   | Abstrak: Artikel ini meninjau...    |                |
|  | [Buka Detail & Sitasi] [Unduh PDF]  |   | [Buka Detail & Sitasi] [Unduh PDF]  |                |
|  +-------------------------------------+   +-------------------------------------+                |
|                                                                                                    |
+----------------------------------------------------------------------------------------------------+
|  © 2026 LENTERA — Universitas Madura (UNIRA) • Fakultas Teknik • Program Studi Informatika         |
+----------------------------------------------------------------------------------------------------+
```
> **Tools:** MockFlow WireframePro ([https://mockflow.com/](https://mockflow.com/)) | **File:** `docs/wireframe/wf-01-beranda.png`

**Anotasi Komponen UI MockFlow:**
- **Header / Navigation Bar**: Brand identity logo UNIRA, tautan menu utama publik, tombol CTA login & pendaftaran akun.
- **Hero Section**: Kotak pencarian prominent berukuran besar dengan tombol aksi primer kontras tinggi.
- **Stat Cards Grid**: 3 kartu ringkasan berbasis angka agregat riil sistem repositori.
- **Katalog Feed Grid**: Kartu karya dua kolom responsif dengan badge tipe karya dan tombol akses cepat.

---

#### 2. Gambar 8.2 — Wireframe: Halaman Pencarian Multi-Filter (WF-02)
*Halaman portal pencarian komprehensif yang dilengkapi bilah penyaring (filter sidebar) multi-kategori dan daftar hasil temuan.*

```
+----------------------------------------------------------------------------------------------------+
|  [Logo UNIRA] LENTERA UNIRA       [Beranda]  [Koleksi Fakultas]  [Statistik]           [Akun Saya] |
+----------------------------------------------------------------------------------------------------+
|  Beranda > Pencarian Naskah Ilmiah                                                                 |
+-----------------------------------+----------------------------------------------------------------+
|  [ FILTER PENCARIAN ]             |  Ditemukan: 14 Karya Ilmiah untuk kata kunci "Kriptografi"      |
|                                   |  Urutkan: [ Terbaru v ]                                        |
|  Kata Kunci:                      +----------------------------------------------------------------+
|  [ Kriptografi                  ] |  +----------------------------------------------------------+  |
|                                   |  | [SKRIPSI] • Tahun 2026 • Program Studi Teknik Informatika |  |
|  Fakultas:                        |  | Implementasi Kriptografi Hill Cipher dan AES pada Dokumen  |  |
|  [ Semua Fakultas               v]|  | Penulis: Imam • Pembimbing: Dr. H. Achmad, M.Kom.         |  |
|                                   |  | Abstrak: Kebutuhan keamanan dokumen akademik menjadi...  |  |
|  Program Studi:                   |  | Kata Kunci: Kriptografi, AES, Hill Cipher, Keamanan      |  |
|  [ Semua Prodi                  v]|  | [👁️ 142 Views] [📥 38 Downloads]   [ Detail & Sitasi ]     |  |
|                                   |  +----------------------------------------------------------+  |
|  Jenis Dokumen:                   |  +----------------------------------------------------------+  |
|  [x] Skripsi                      |  | [JURNAL] • Tahun 2025 • Program Studi Sistem Informasi   |  |
|  [ ] Tesis                        |  | Studi Komparasi Algoritma Enkripsi Citra Medis Digital     |  |
|  [x] Jurnal Ilmiah                |  | Penulis: Alvin H. • Pembimbing: Budi S., M.T.            |  |
|  [ ] Kerja Praktik                |  | Abstrak: Penelitian komparatif ini mengukur kecepatan... |  |
|                                   |  | [👁️ 98 Views]  [📥 24 Downloads]   [ Detail & Sitasi ]     |  |
|  Rentang Tahun:                   |  +----------------------------------------------------------+  |
|  Dari: [ 2022 ]  Sampai: [ 2026 ] |                                                                |
|                                   |  Halaman: [ < Sebelumnya ]  [1]  [2]  [3]  [ Selanjutnya > ]   |
|  [ Terapkan Filter ] [ Reset ]    |                                                                |
+-----------------------------------+----------------------------------------------------------------+
```
> **Tools:** MockFlow WireframePro ([https://mockflow.com/](https://mockflow.com/)) | **File:** `docs/wireframe/wf-02-pencarian.png`

**Anotasi Komponen UI MockFlow:**
- **Filter Sidebar (Kiri 30%)**: Kontrol input kata kunci, dropdown fakultas/prodi, checkbox jenis dokumen, dan rentang tahun.
- **Results Canvas (Kanan 70%)**: Informasi total temuan naskah, dropdown sorting, kartu hasil pencarian dengan badge metrik unduhan, dan pagination kontrol.

---

#### 3. Gambar 8.3 — Wireframe: Halaman Detail Naskah & Generator Sitasi (WF-03)
*Halaman penampil abstrak lengkap naskah yang mengintegrasikan generator sitasi otomatis multi-format (APA, IEEE, Harvard, BibTeX) dan tombol unduh PDF resmi.*

```
+----------------------------------------------------------------------------------------------------+
|  [Logo UNIRA] LENTERA UNIRA       [Beranda]  [Koleksi Fakultas]  [Statistik]           [Akun Saya] |
+----------------------------------------------------------------------------------------------------+
|  Beranda > Teknik Informatika > Skripsi > Detail Naskah #104                                       |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|  [SKRIPSI SARJANA] • Diterbitkan pada: 02 November 2026 • Status: RESMI TERBIT (PUBLISHED)         |
|  # Rancang Bangun Repositori Ilmiah Kampus dengan Arsitektur Multi-Tier dan Enkripsi PDF           |
|  Penulis: Imam (NIM: 2024520030) • Fakultas Teknik • Program Studi Teknik Informatika              |
|  Dosen Pembimbing I: Dr. Achmad, M.Kom. • Dosen Pembimbing II: Fajar, M.Kom.                       |
|                                                                                                    |
|  +----------------------------------------------------------------------------------------------+  |
|  | ABSTRAK                                                                                      |  |
|  | Pengelolaan arsip karya ilmiah fisik seringkali menghadapi kendala keterbatasan ruang fisik  |  |
|  | dan kesulitan temu balik literatur bagi adik tingkat. Penelitian ini membangun sistem...     |  |
|  | Kata Kunci: Repositori Digital, Multiplatform, Flutter, MVC Native, OWASP Security           |  |
|  +----------------------------------------------------------------------------------------------+  |
|                                                                                                    |
|  +-- GENERATOR SITASI ILMIAH OTOMATIS ----------------------------------------------------------+  |
|  |  [ Tab APA 7th ]  [ Tab IEEE ]  [ Tab Harvard ]  [ Tab BibTeX ]                              |  |
|  |  +----------------------------------------------------------------------------------------+  |  |
|  |  | Imam. (2026). Rancang Bangun Repositori Ilmiah Kampus dengan Arsitektur Multi-Tier...  |  |  |
|  |  | Universitas Madura.                                                                    |  |  |
|  |  +----------------------------------------------------------------------------------------+  |  |
|  |  [ 📋 Salin Sitasi ke Clipboard ]   (✓ Tersalin otomatis ke daftar pustaka!)                 |  |
|  +----------------------------------------------------------------------------------------------+  |
|                                                                                                    |
|  [ 📥 UNDUH NASKAH RESMI (PDF UTUH - 4.2 MB) ]    [ 🔗 Bagikan Tautan Naskah ]                      |
|  * Setiap unduhan tercatat dalam log audit kampus demi integritas akademik UNIRA.                  |
|                                                                                                    |
+----------------------------------------------------------------------------------------------------+
```
> **Tools:** MockFlow WireframePro ([https://mockflow.com/](https://mockflow.com/)) | **File:** `docs/wireframe/wf-03-detail-sitasi.png`

**Anotasi Komponen UI MockFlow:**
- **Header Metadata**: Label jenjang dokumen, tanggal terbit resmi, identitas penulis dan pembimbing.
- **Abstract Viewport**: Teks abstrak bahasa Indonesia berbingkai bersih dengan daftar kata kunci yang dapat diklik.
- **Citation Box**: Komponen *Tab Switching* yang mengubah teks sitasi secara real-time dan tombol copy satu klik.
- **Action Zone**: Tombol primer unduh berkas PDF yang terhubung ke skrip *indirect streaming*.

---

#### 4. Gambar 8.4 — Wireframe: Dasbor Karya Penulis & Formulir Submitting (WF-04)
*Ruang kerja pribadi mahasiswa/dosen untuk memantau status naskah yang diajukan serta formulir unggah naskah baru.*

```
+----------------------------------------------------------------------------------------------------+
|  [Logo UNIRA] LENTERA         [Katalog Publik]  |  Halo, Imam (Penulis)               [ Keluar ]   |
+----------------------------------------------------------------------------------------------------+
|  +----------------------+  +--------------------------------------------------------------------+  |
|  | DASBOR PENULIS       |  |  KARYA ILMIAH SAYA                                  [ + Ajukan Naskah ]|  |
|  |                      |  +--------------------------------------------------------------------+  |
|  | [•] Karya Saya       |  |  Judul Karya                 | Tipe    | Status Kurasi   | Aksi         |  |
|  | [ ] Ajukan Naskah    |  |------------------------------|---------|-----------------|--------------|  |
|  | [ ] Riwayat Revisi   |  |  Rancang Bangun Repositori...| Skripsi | [🟢 Published]  | [Lihat]      |  |
|  | [ ] Pembayaran APC   |  |  Analisis Keamanan Jaringan..| Jurnal  | [🟠 Revision]   | [Unggah Rev] |  |
|  | [ ] Profil Akun      |  |  Sistem Pakar Diagnosa Padi..| Skripsi | [🟣 Wait Pay]   | [Bayar APC]  |  |
|  |                      |  +--------------------------------------------------------------------+  |
|  +----------------------+                                                                          |
|                                                                                                    |
|  --- FORMULIR PENGAJUAN NASKAH BARU (creates.php) -------------------------------------------------|
|  Judul Karya Ilmiah (min. 10 karakter):                                                            |
|  [ Masukkan judul lengkap naskah skripsi / penelitian...                                        ]  |
|                                                                                                    |
|  Teks Abstrak (min. 30 karakter):                                                                  |
|  [ Tuliskan ringkasan latar belakang, metodologi, dan hasil temuan penelitian...                 ]  |
|  [                                                                                               ]  |
|                                                                                                    |
|  Kata Kunci: [ AI, Machine Learning, Python ]    Tahun Selesai: [ 2026 v ]    Tipe: [ Skripsi v ]  |
|  Dosen Pembimbing I: [ Dr. H. Achmad, M.Kom. ]   Dosen Pembimbing II: [ Fajar, M.Kom.            ]  |
|                                                                                                    |
|  Unggah Berkas Naskah PDF (Maksimal 15 MB):                                                        |
|  +----------------------------------------------------------------------------------------------+  |
|  |  📄 Seret & lepas file PDF di sini, atau [ Pilih Berkas dari Komputer ]                       |  |
|  |  Format diizinkan: .pdf asli (Header %PDF- divalidasi sistem)                                |  |
|  +----------------------------------------------------------------------------------------------+  |
|                                                                                                    |
|  [ Batalkan ]                                                [ 🚀 Ajukan Naskah ke Dewan Kurator ] |
+----------------------------------------------------------------------------------------------------+
```
> **Tools:** MockFlow WireframePro ([https://mockflow.com/](https://mockflow.com/)) | **File:** `docs/wireframe/wf-04-dasbor-penulis.png`

**Anotasi Komponen UI MockFlow:**
- **Sidebar Navigasi Penulis**: Menu cepat Karya Saya, Pengajuan, Revisi, dan Billing APC.
- **Works Status Table**: Tabel naskah pribadi dengan label status warna (Hijau = Published, Oranye = Revision, Ungu = Waiting Payment).
- **Submitting Form Fields**: Input judul, teks area abstrak, dropdown prodi/tahun/pembimbing, dan area *drag-and-drop* uploader PDF.

---

#### 5. Gambar 8.5 — Wireframe: Ruang Kerja Antrean Kurasi & Modal Keputusan OJS (WF-05)
*Antarmuka bagi Dewan Kurator untuk memeriksa naskah yang masuk antrean, melihat skor kesamaan (similarity), membaca Galley PDF, dan menetapkan keputusan telaah.*

```
+----------------------------------------------------------------------------------------------------+
|  [Logo UNIRA] LENTERA         [Katalog Publik]  |  Dewan Kurator: Dr. H. Achmad       [ Keluar ]   |
+----------------------------------------------------------------------------------------------------+
|  RUANG KERJA DEWAN KURASI (OJS EDITORIAL QUEUE)                                                    |
|  Filter Prodi: [ Teknik Informatika v ]   Status: [ Semua Antrean v ]   Urutan: [ Terlama v ]      |
+----------------------------------------------------------------------------------------------------+
|  Judul Naskah Masuk         | Penulis & NIM        | Tanggal Masuk | Similarity | Aksi Kurasi      |
|-----------------------------|----------------------|---------------|------------|------------------|
|  Rancang Bangun Repositori..| Imam (2024520030)    | 02/11/2026    | [ 12% OK ] | [Telaah Naskah]  |
|  Optimasi Query Database... | Alvin H. (2024520050)| 03/11/2026    | [ 28% WARN]| [Telaah Naskah]  |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|  +-- MODAL FORM KEPUTUSAN KURATOR (POP-UP DIALOG) -----------------------------------------------+  |
|  |  Telaah Naskah: Rancang Bangun Repositori Ilmiah Kampus (Versi 1)                             |  |
|  |  Penulis: Imam (2024520030) • Program Studi Teknik Informatika                                |  |
|  |  Berkas Naskah: [ 📄 Buka Galley PDF di Tab Baru ] • Indeks Kemiripan: 12% (Lolos Batas Maks) |  |
|  |                                                                                               |  |
|  |  TETAPKAN KEPUTUSAN EDITORIAL:                                                                |  |
|  |  ( ) Setujui & Terbitkan (Approve) — Naskah memenuhi standar mutu ilmiah                     |  |
|  |  (•) Minta Penulis Revisi (Revision) — Naskah perlu perbaikan substansi                       |  |
|  |  ( ) Tolak Naskah (Reject) — Naskah melanggar etika ilmiah / plagiasi berat                   |  |
|  |                                                                                               |  |
|  |  Catatan Evaluasi / Masukan Perbaikan Kurator (Wajib diisi untuk opsi Revisi):               |  |
|  |  +-----------------------------------------------------------------------------------------+  |  |
|  |  | 1. Perbaiki penulisan abstrak bahasa Inggris agar mencakup metodologi kuantitatif.      |  |  |
|  |  | 2. Perbarui daftar pustaka menggunakan minimal 10 referensi jurnal terakreditasi 5 thn...|  |  |
|  |  +-----------------------------------------------------------------------------------------+  |  |
|  |                                                                                               |  |
|  |  [ Batal / Tutup ]                                        [ 💾 Simpan Keputusan Editorial ]  |  |
|  +-----------------------------------------------------------------------------------------------+  |
+----------------------------------------------------------------------------------------------------+
```
> **Tools:** MockFlow WireframePro ([https://mockflow.com/](https://mockflow.com/)) | **File:** `docs/wireframe/wf-05-antrean-kurasi.png`

**Anotasi Komponen UI MockFlow:**
- **Antrean Kurasi Table**: Tabel daftar naskah masuk dengan kolom indikator plagiasi berwarna (*Green* $\le 25\%$, *Red* $>25\%$).
- **Galley PDF Trigger**: Tombol pembuka berkas PDF naskah utuh pada viewer tab baru.
- **Editorial Decision Radio Group**: Pilihan radio keputusan bertingkat (*Approve*, *Revision*, *Reject*).
- **Feedback Textarea**: Kolom catatan telaah wajib untuk menginstruksikan poin-poin perbaikan naskah.

---

#### 6. Gambar 8.6 — Wireframe: Portal Pembayaran APC & Unggah Bukti Bayar (WF-06)
*Antarmuka bagi penulis naskah yang disetujui kurator untuk menyelesaikan administrasi biaya publikasi menggunakan saluran QRIS resmi UNIRA.*

```
+----------------------------------------------------------------------------------------------------+
|  [Logo UNIRA] LENTERA         [Katalog Publik]  |  Halo, Imam (Penulis)               [ Keluar ]   |
+----------------------------------------------------------------------------------------------------+
|  Beranda > Dasbor Karya > Pembayaran APC Naskah #104                                               |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|  +-- RINCIAN INVOICE BIAYA PUBLIKASI (APC) ------------------------------------------------------+  |
|  |  Nomor Invoice : INV-2026-11-0042           Tanggal Tagihan : 03 November 2026                |  |
|  |  Judul Naskah  : Rancang Bangun Repositori Ilmiah Kampus dengan Arsitektur Multi-Tier         |  |
|  |  Status Kurasi : [🟢 DISETUJUI DEWAN KURATOR]                                                 |  |
|  |  Total Tagihan : Rp 150.000,00 (Seratus Lima Puluh Ribu Rupiah)                               |  |
|  +-----------------------------------------------------------------------------------------------+  |
|                                                                                                    |
|  +-- METODE PEMBAYARAN RESMI UNIVERSITAS MADURA -------------------------------------------------+  |
|  |                                                                                               |  |
|  |     +-------------------------+      PANDUAN PEMBAYARAN QRIS / TRANSFER:                      |  |
|  |     |                         |      1. Buka aplikasi M-Banking (BCA, Mandiri, BNI, BRI)      |  |
|  |     |   [ BARCODE QRIS ]      |         atau E-Wallet (Gopay, OVO, Dana, LinkAja).            |  |
|  |     |   NMID: ID1029384756    |      2. Pindai kode QRIS di samping atau transfer ke:         |  |
|  |     |   LENTERA UNIRA OFFICIAL|         Bank Jatim: 028-102-9938 a.n. Universitas Madura      |  |
|  |     |                         |      3. Simpan bukti transfer / struk pembayaran fisik.       |  |
|  |     +-------------------------+                                                               |  |
|  +-----------------------------------------------------------------------------------------------+  |
|                                                                                                    |
|  --- KONFIRMASI PEMBAYARAN & UNGGAH STRUK ---------------------------------------------------------|
|  Nama Pengirim di Rekening: [ Imam                                                           ]     |
|  Nomor Referensi / Ref ID : [ TRX-992018302                                                  ]     |
|                                                                                                    |
|  Unggah Foto Bukti Transfer / Struk (Maks. 5 MB):                                                  |
|  +----------------------------------------------------------------------------------------------+  |
|  |  📸 [ Pilih Foto Bukti Pembayaran (.jpg, .png, .pdf) ]   struk_transfer_apc.jpg (420 KB)     |  |
|  +----------------------------------------------------------------------------------------------+  |
|                                                                                                    |
|  [ Kembali ke Dasbor ]                                           [ 💳 Kirim Konfirmasi Bukti ]     |
+----------------------------------------------------------------------------------------------------+
```
> **Tools:** MockFlow WireframePro ([https://mockflow.com/](https://mockflow.com/)) | **File:** `docs/wireframe/wf-06-portal-apc.png`

**Anotasi Komponen UI MockFlow:**
- **Invoice Summary Card**: Menampilkan nomor tagihan unik, judul naskah, dan nominal tarif publikasi.
- **QRIS Code Frame**: Kotak penyajian barcode QRIS resmi kampus beserta instruksi langkah transfer perbankan.
- **Proof Uploader & Meta Input**: Kolom nama penyetor, nomor referensi mutasi bank, dan tombol unggah berkas bukti bayar.

---

#### 7. Gambar 8.7 — Wireframe: Panel Super Admin & Verifikasi Bukti APC (WF-07)
*Dasbor kendali administrator kampus untuk memeriksa struk transfer, memverifikasi pelunasan, menerbitkan karya ke publik, serta mengelola data pengguna.*

```
+----------------------------------------------------------------------------------------------------+
|  [Logo UNIRA] LENTERA ADMIN   [Tab Verifikasi APC (3)]  [Tab Kelola User]  [Tab Master]   [Keluar] |
+----------------------------------------------------------------------------------------------------+
|  PANEL VERIFIKASI PEMBAYARAN BIAYA PUBLIKASI (APC)                                                 |
+----------------------------------------------------------------------------------------------------+
|  Invoice       | Judul Naskah              | Penulis    | Nominal     | Bukti Bayar  | Aksi        |
|----------------|---------------------------|------------|-------------|--------------|-------------|
|  INV-2026-0042 | Rancang Bangun Repositori | Imam       | Rp 150.000  | [Lihat Struk]| [✓ Terima]  |
|                |                           |            |             |              | [✗ Tolak]   |
|  INV-2026-0043 | Sistem Informasi Absensi  | Gilank A.  | Rp 150.000  | [Lihat Struk]| [✓ Terima]  |
|                |                           |            |             |              | [✗ Tolak]   |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|  +-- MODAL VIEWER STRUK BUKTI TRANSFER (view_proofs.php) ----------------------------------------+  |
|  |  Verifikasi Pembayaran Invoice: INV-2026-0042 (Penulis: Imam - NIM: 2024520030)               |  |
|  |                                                                                               |  |
|  |  +---------------------------------------------------+  Data Transaksi Bank:                  |  |
|  |  |                                                   |  • Pengirim : Imam                     |  |
|  |  |   [ GAMBAR STRUK TRANSFER RESMI ASLI DARI BANK ]  |  • Ref ID   : TRX-992018302            |  |
|  |  |   Status: Berhasil ditransfer ke Rek UNIRA        |  • Nominal  : Rp 150.000 (Pas)         |  |
|  |  |   Tanggal: 03/11/2026 09:14 WIB                   |  • Mutasi   : DITEMUKAN DI REKENING    |  |
|  |  |                                                   |                                        |  |
|  |  +---------------------------------------------------+                                        |  |
|  |                                                                                               |  |
|  |  Catatan Admin (opsional jika tolak): [                                                    ]  |  |
|  |                                                                                               |  |
|  |  [ Tutup Dialog ]             [ ✗ Tolak Bukti Bayar ]        [ ✓ VERIFIKASI & TERBITKAN RESMI]|  |
|  +-----------------------------------------------------------------------------------------------+  |
+----------------------------------------------------------------------------------------------------+
```
> **Tools:** MockFlow WireframePro ([https://mockflow.com/](https://mockflow.com/)) | **File:** `docs/wireframe/wf-07-admin-verifikasi.png`

**Anotasi Komponen UI MockFlow:**
- **Admin Tab Navigation**: Tab pemisah antara Verifikasi Keuangan, Manajemen Akun Pengguna, dan Master Data.
- **Verification Queue Table**: Tabel antrean transaksi berstatus `pending_verification`.
- **Proof Viewer Modal (`view_proofs.php`)**: Penampil aman foto struk pembayaran bersebelahan dengan data mutasi rekening bank kampus.
- **Atomic Publish CTA**: Tombol hijau *"Verifikasi & Terbitkan"* yang secara atomik mengubah status pembayaran dan menerbitkan karya ke publik.

---

#### 8. Gambar 8.8 — Wireframe: Aplikasi Mobile Native Flutter (WF-08)
*Cetak biru tata letak aplikasi mobile native pada layar smartphone ($375 \times 812\text{px}$) mencakup beranda, chip filter fakultas, penampil abstrak ramah mata, dan penanganan offline banner.*

```
+-----------------------------------+   +-----------------------------------+
|  [📱 Layar Beranda Mobile]         |   |  [📱 Layar Detail & Sitasi Mobile]|
|  09:41                        📶🔋|   |  09:41                        📶🔋|
|  🏛️ LENTERA UNIRA                 |   |  <- Kembali          [❤️]  [📤 Bagikan]|
|  Repositori Ilmiah Kampus         |   |                                   |
|  +-----------------------------+  |   |  [SKRIPSI SARJANA] • 2026         |
|  | 🔍 Cari judul / penulis...  |  |   |  Rancang Bangun Repositori        |
|  +-----------------------------+  |   |  Ilmiah Kampus dengan Flutter     |
|                                   |   |  Penulis: Imam (2024520030)       |
|  Kategori Fakultas:               |   |  Fakultas Teknik - Informatika    |
|  [Semua] [FT] [FE] [FH] [FKIP]    |   |                                   |
|                                   |   |  +-- ABSTRAK LENGKAP -----------+ |
|  Naskah Terbaru:                  |   |  | Pengelolaan arsip karya      | |
|  +-----------------------------+  |   |  | ilmiah memerlukan integrasi  | |
|  | [SKRIPSI] 2026              |  |   |  | mobile multiplatform...      | |
|  | Rancang Bangun Repositori...|  |   |  +------------------------------+ |
|  | Penulis: Imam               |  |   |                                   |
|  | Teknik Informatika          |  |   |  +-- SALIN SITASI CEPAT --------+ |
|  | [Detail & Sitasi] [📥 PDF]  |  |   |  | Format: [ APA 7th Edition v] | |
|  +-----------------------------+  |   |  | [ 📋 Salin ke Clipboard ]    | |
|  +-----------------------------+  |   |  +------------------------------+ |
|  | [JURNAL] 2026               |  |   |                                   |
|  | Optimasi Algoritma Hill...  |  |   |  +------------------------------+ |
|  | Penulis: Alvin H.           |  |   |  | 📥 UNDUH PDF (4.2 MB)        | |
|  +-----------------------------+  |   |  +------------------------------+ |
|                                   |   |                                   |
|  [⚠️ Tidak ada koneksi internet]  |   |                                   |
|  +-----------------------------+  |   |                                   |
|  | [🏠 Home] [🔍 Cari] [👤 Akun]|  |   |                                   |
|  +-----------------------------+  |   +-----------------------------------+
+-----------------------------------+
```
> **Tools:** MockFlow WireframePro ([https://mockflow.com/](https://mockflow.com/)) | **File:** `docs/wireframe/wf-08-mobile-flutter.png`

**Anotasi Komponen UI MockFlow:**
- **Mobile Search Header**: Kolom pencarian ringkas teroptimasi sentuhan jempol (*thumb-zone friendly*).
- **Horizontal Chip Filters**: Bilah filter geser horizontal (*horizontal scrollable chips*) untuk pemilihan fakultas.
- **Card Feed**: Kartu karya satu kolom dengan target tap minimal $44\text{px}$.
- **Offline Banner**: Penanda visual kuning adaptif saat koneksi internet terputus.
- **Bottom Navigation Bar**: Navigasi bawah 3 tab (Home, Cari, Akun Saya).

---

<div align="center">
  <b>Sistem Repositori LENTERA — Universitas Madura (UNIRA)</b><br/>
  <sub>Disusun oleh Kelompok 01 (Imam, Alvin, Gilank, Halim) • PBP 2026</sub>
</div>
