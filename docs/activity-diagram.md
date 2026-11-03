# 2.3 Activity Diagram — Sistem Repositori LENTERA
### Laman Etalase Karya Tulis Ilmiah Terpadu Akademik Universitas Madura (UNIRA)

Dokumen ini menyajikan pemodelan **Activity Diagram** berstandar StarUML dan PlantUML untuk 5 (lima) alur proses bisnis inti dan tersulit dalam platform LENTERA. Setiap diagram menggunakan notasi UML baku: *initial node* (oval hitam), *activity state* (rounded-rect), *decision node* (diamond percabangan), *merge/join bar*, serta partisi jalur tanggung jawab (*swimlanes*) antar-aktor dan sistem.

---

### Daftar 5 Diagram Alur Aktivitas:
1. **Gambar 6.1**: Alur Pengajuan Mandiri Naskah Baru & Validasi Berkas (*Penulis & Sistem*)
2. **Gambar 6.2**: Alur Penelaahan Sejawat OJS & Evaluasi Editorial Berulang (*Kurator, Sistem, & Penulis*)
3. **Gambar 6.3**: Alur Transaksi APC, Verifikasi Bukti Bayar & Penerbitan Karya (*Penulis, Sistem, & Super Admin*)
4. **Gambar 6.4**: Alur Pencarian Naskah, Sitasi 1-Klik & Unduhan PDF Terproteksi (*Publik & Sistem*)
5. **Gambar 6.5**: Alur Manajemen Pengguna & Pengangkatan Peran Kurator (*Super Admin, Sistem, & Dosen Kurator*)

---

### 1. Gambar 6.1 — Alur Pengajuan Mandiri Naskah Baru & Validasi Berkas

Diagram ini memodelkan proses awal saat penulis mengajukan naskah akademik baru, mencakup pemeriksaan keamanan token CSRF, validasi integritas biner PDF, penyimpanan direktori terisolasi, hingga pencatatan status `'submitted'`.

```mermaid
flowchart TD
    subgraph Penulis ["👨‍🎓 Penulis (Author)"]
        startNode((●)) --> A1["1. Buka Menu Unggah Naskah (/workss/creates.php)"]
        A1 --> A2["2. Isi Judul, Abstrak, Kata Kunci & Pembimbing"]
        A2 --> A3["3. Pilih File Naskah PDF dari Komputer"]
        A3 --> A4["4. Klik Tombol 'Ajukan Naskah ke Kurator'"]
        A7["8. Terima Konfirmasi 'Naskah Berhasil Diajukan'"] --> endNode((◎))
        A8["8a. Perbaiki Pilihan Berkas Naskah"] --> endErr((◎))
    end

    subgraph Sistem ["⚙️ Sistem LENTERA"]
        A4 --> S1{"5. Validasi Token CSRF Sesi?"}
        S1 -- Tidak Valid --> S_ERR1["Blokir Akses (403 Forbidden)"] --> endErr
        S1 -- Valid --> S2{"6. Cek Format (.pdf), Magic Bytes %PDF- & Ukuran <= 15MB?"}
        S2 -- Tidak Valid --> S_ERR2["7. Tampilkan Pesan Galat Berkas"] --> A8
        S2 -- Valid --> S3["7. Generate Nama Acak Unik (UUID/Hash)"]
        S3 --> S4["Simpan PDF ke /uploads/works/"]
        S4 --> S5["Insert Record ke Tabel works (Status: 'submitted')"]
        S5 --> S6["Insert ke work_files (version: 1, is_current: 1)"]
        S6 --> S7["Insert Indeks Kata Kunci ke work_keywords"]
        S7 --> A7
    end
```
> **Tools:** StarUML v5.x / PlantUML | **File:** `docs/diagram/activity_pengajuan_naskah.puml`

#### Narasi Alur 6.1 (10 Langkah):
1. **Penulis** mengakses formulir pengajuan naskah ilmiah baru melalui menu navigasi pribadi (`/workss/creates.php`).
2. **Penulis** melengkapi seluruh isian metadata akademik: judul naskah (minimal 10 karakter), abstrak lengkap (minimal 30 karakter), kata kunci penelitian, program studi, tahun kelulusan, serta dosen pembimbing I & II.
3. **Penulis** memilih berkas dokumen naskah format PDF dari media penyimpanan lokal komputernya.
4. **Penulis** menekan tombol *"Ajukan Naskah ke Kurator"*.
5. **Sistem** memverifikasi keabsahan token keamanan anti-CSRF pada sesi formulir yang dikirimkan.
6. **Sistem** melakukan validasi lapis ganda terhadap berkas: memastikan ekstensi biner `.pdf`, memeriksa *magic bytes* pembuka `%PDF-`, serta memastikan ukuran berkas tidak melampaui ambang batas $15\text{ MB}$.
7. Jika berkas tidak valid, **Sistem** membatalkan proses dan menampilkan pesan kesalahan edukatif kepada penulis.
8. Jika berkas valid, **Sistem** menamai ulang berkas secara acak (*obfuscated hash*) guna mencegah konflik nama serta menyimpan file fisik ke direktori terisolasi `uploads/works/`.
9. **Sistem** secara atomik menyimpan data naskah ke tabel `works` dengan status awal mutlak `'submitted'`, mencatat versi pertama pada `work_files`, dan mengindeks kata kunci pada `work_keywords`.
10. **Penulis** menerima notifikasi sukses dan melihat karya ilmiahnya telah terdaftar pada dasbor pribadi dalam status antre kurasi.

---

### 2. Gambar 6.2 — Alur Penelaahan Sejawat OJS & Evaluasi Editorial Berulang

Diagram ini memodelkan siklus penelaahan kualitas karya ilmiah (*peer-review workflow*) ala Open Journal Systems (OJS), termasuk evaluasi plagiasi (*similarity*), pembacaan galley PDF, penerbitan, penolakan, serta siklus revisi berulang (*iterative revision cycle*).

```mermaid
flowchart TD
    subgraph Kurator ["⚖️ Dewan Kurator"]
        startNode((●)) --> K1["1. Buka Dasbor Antrean Kurasi (/reviews/indexs.php)"]
        K1 --> K2["2. Pilih Naskah & Klik 'Tandai In Review'"]
        K4["4. Buka & Pelajari Naskah Galley PDF"]
        K5{"5. Evaluasi Keputusan Kurator?"}
        K_REV["6b. Pilih Opsi 'Revision' & Tulis Catatan Telaah"]
        K_APP["6a. Pilih Opsi 'Approve' (Setujui)"]
        K_REJ["6c. Pilih Opsi 'Reject' (Tolak)"]
    end

    subgraph Sistem ["⚙️ Sistem LENTERA"]
        K2 --> S1["Update status = 'in_review' di Tabel works"]
        S1 --> S2["Tampilkan Similarity Score & Tombol Galley PDF"]
        S2 --> K4
        K4 --> K5
        K_APP --> S_APP["Update status = 'waiting_payment' & Simpan reviews"]
        K_REJ --> S_REJ["Update status = 'rejected' & Simpan reviews"]
        K_REV --> S_REV["Simpan Catatan ke reviews & Update status = 'revision'"]
        S_REV --> P1
        S_REVISI_NEW["Update is_current = 0 versi lama\nInsert versi baru (v2, is_current = 1)\nUpdate status = 'submitted'"] --> K_NOTIF["Kirim Kembali ke Antrean Kurator"] --> K1
    end

    subgraph Penulis ["👨‍🎓 Penulis (Author)"]
        P1["7. Baca Catatan Telaah di Dasbor Pribadi"]
        P1 --> P2["8. Buka Formulir Revisi (/workss/revises.php)"]
        P2 --> P3["9. Upload PDF Revisi Baru & Tulis Respons"]
        P3 --> P4["10. Klik 'Kirim Dokumen Revisi'"]
        P4 --> S_REVISI_NEW
        S_APP --> P_PAY["Terima Status Menunggu Pembayaran APC"] --> endNode((◎))
        S_REJ --> P_REJ["Terima Status Naskah Ditolak"] --> endNode
    end
```
> **Tools:** StarUML v5.x / PlantUML | **File:** `docs/diagram/activity_kurasi_ojs.puml`

#### Narasi Alur 6.2 (12 Langkah):
1. **Dewan Kurator** masuk ke ruang kerja antrean kurasi (`/reviews/indexs.php`) untuk melihat daftar naskah civitas yang masuk.
2. **Kurator** memilih salah satu naskah dan mengklik aksi *"Tandai In Review"* agar penelaah lain mengetahui naskah sedang aktif dipelajari.
3. **Sistem** memperbarui status naskah menjadi `in_review` dan menyajikan data skor kemiripan (*similarity score*).
4. **Kurator** membuka tautan aman berkas *Galley PDF* untuk membaca konten, metodologi, dan memeriksa orisinalitas naskah.
5. **Kurator** membuka formulir modal keputusan editorial dan mempertimbangkan 3 opsi keputusan: *Approve*, *Revision*, atau *Reject*.
6. Jika naskah melanggar etika ilmiah/plagiasi berat, **Kurator** memilih *Reject*, dan **Sistem** mengunci naskah pada status `rejected`.
7. Jika naskah telah memenuhi standar akademik, **Kurator** memilih *Approve*, dan **Sistem** mengalihkan status naskah ke `waiting_payment` (atau langsung `published`).
8. Jika naskah membutuhkan perbaikan, **Kurator** memilih *Revision* dan wajib mengetikkan poin-poin catatan telaah pada kolom evaluasi.
9. **Sistem** menyimpan keputusan telaah ke tabel `reviews` dan memperbarui status naskah menjadi `revision`.
10. **Penulis** melihat peringatan revisi pada dasbornya, membaca catatan kurator, dan membuka formulir pengunggahan naskah revisi (`/workss/revises.php`).
11. **Penulis** mengunggah berkas PDF naskah yang telah disempurnakan lalu menekan tombol *"Kirim Dokumen Revisi"*.
12. **Sistem** secara otomatis menonaktifkan versi dokumen sebelumnya (`is_current = 0`), menerbitkan nomor versi baru (`version = 2`, `is_current = 1`), dan mengembalikan status naskah menjadi `'submitted'` untuk ditelaah kembali oleh kurator.

---

### 3. Gambar 6.3 — Alur Transaksi APC, Verifikasi Bukti Bayar & Penerbitan Karya

Diagram ini memodelkan proses penyelesaian administrasi biaya publikasi (*Article Processing Charge / APC*), unggah bukti bayar oleh penulis, pencocokan mutasi oleh Super Admin, hingga penerbitan naskah secara atomik ke katalog publik.

```mermaid
flowchart TD
    subgraph Penulis ["👨‍🎓 Penulis (Author)"]
        startNode((●)) --> P1["1. Buka Dasbor Karya (Status: Waiting Payment)"]
        P1 --> P2["2. Klik 'Bayar Biaya Publikasi APC' (/workss/payments.php)"]
        P3["4. Lakukan Transfer via QRIS / M-Banking UNIRA"]
        P3 --> P4["5. Foto Struk / Tangkapan Layar Bukti Transfer"]
        P4 --> P5["6. Upload Berkas Bukti & Klik 'Kirim Konfirmasi'"]
        P_SUCCESS["11. Terima Status 'Published' & Naskah Tayang Publik"] --> endNode((◎))
        P_RETRY["11a. Baca Alasan Penolakan & Upload Ulang Struk"] --> P4
    end

    subgraph Sistem ["⚙️ Sistem LENTERA"]
        P2 --> S1["3. Tampilkan Invoice Tagihan & Gambar Barcode QRIS"]
        S1 --> P3
        P5 --> S2["7. Validasi Format Berkas Struk & Simpan ke /uploads/payment/"]
        S2 --> S3["Simpan Record ke Tabel payments (Status: 'pending_verification')"]
        S3 --> A1
        A_VERIF -- Valid --> S_PUB["10. Update payments = 'verified'\nAtomik Update works = 'published'\nSet published_at = NOW()\nCatat ke activity_logs"] --> P_SUCCESS
        A_VERIF -- Tidak Valid --> S_REJ["10a. Update payments = 'rejected'\nSimpan Catatan Penolakan"] --> P_RETRY
    end

    subgraph Admin ["🛡️ Super Admin"]
        A1["8. Buka Panel Admin Tab Verifikasi APC"]
        A1 --> A2["9. Buka Viewer Struk Pembayaran (view_proofs.php)"]
        A2 --> A_VERIF{"Cek Kesesuaian Mutasi Rekening?"}
    end
```
> **Tools:** StarUML v5.x / PlantUML | **File:** `docs/diagram/activity_pembayaran_apc.puml`

#### Narasi Alur 6.3 (11 Langkah):
1. **Penulis** melihat naskahnya telah disetujui kurator dengan status *"Menunggu Pembayaran"* pada dasbor karya.
2. **Penulis** mengklik tombol aksi *"Bayar Biaya Publikasi APC"* untuk membuka portal transaksi (`/workss/payments.php`).
3. **Sistem** menampilkan rincian nomor invoice, besaran nominal tagihan resmi, serta barcode QRIS resmi Universitas Madura.
4. **Penulis** menyelesaikan transaksi melalui transfer perbankan atau pemindaian QRIS lewat aplikasi mobile banking.
5. **Penulis** menyimpan bukti transfer dan mengunggahnya pada kolom konfirmasi pembayaran.
6. **Penulis** menekan tombol *"Kirim Konfirmasi Pembayaran"*.
7. **Sistem** memvalidasi tipe file bukti transfer, menyimpan berkas ke `uploads/payment/`, dan membuat entri baru pada tabel `payments` dengan status `pending_verification`.
8. **Super Admin** membuka panel kendali (`/admins/indexs.php`) pada tab *"Verifikasi Pembayaran APC"*.
9. **Super Admin** membuka penampil berkas aman (`view_proofs.php`) untuk mencocokkan mutasi rekening koran kampus.
10. Jika bukti transfer terbukti palsu atau tidak sesuai nominal, **Admin** menolak bukti bayar disertai alasan, dan **Sistem** mengembalikan status pembayaran ke `rejected` agar penulis mengunggah ulang.
11. Jika bukti transfer valid, **Admin** menekan tombol *"Verifikasi & Terbitkan"*, sehingga **Sistem** secara atomik mengubah status pembayaran menjadi `verified`, mengubah status naskah menjadi `published`, mengisi `published_at = NOW()`, dan naskah langsung resmi tayang di etalase publik.

---

### 4. Gambar 6.4 — Alur Pencarian Naskah, Sitasi 1-Klik & Unduhan PDF Terproteksi

Diagram ini memodelkan interaksi publik tanpa login saat menelusuri literatur ilmiah, menyalin sitasi multi-format, hingga mengunduh naskah PDF resmi yang disertai pencatatan jejak audit (*audit logging*).

```mermaid
flowchart TD
    subgraph Publik ["🌐 Publik (Guest / Mahasiswa)"]
        startNode((●)) --> G1["1. Buka Beranda / Halaman Pencarian (/search)"]
        G1 --> G2["2. Ketik Kata Kunci & Pilih Filter Fakultas/Prodi/Tahun"]
        G2 --> G3["3. Tekan Tombol 'Cari Naskah'"]
        G_EMPTY["5a. Lihat Tampilan Empty State / Reset Filter"] --> endNode((◎))
        G4["5. Pilih & Klik Judul Naskah yang Relevan"]
        G5["7. Klik Tombol 'Salin Sitasi (APA)'"]
        G6["9. Klik Tombol 'Unduh Naskah PDF'"]
        G7["11. Peramban Menerima & Mengunduh Berkas PDF Resmi"] --> endNode
    end

    subgraph Sistem ["⚙️ Sistem LENTERA"]
        G3 --> S1["4. Sanitasi Parameter & Query Database (status = 'published')"]
        S1 --> S_CHECK{"Karya Ditemukan?"}
        S_CHECK -- Tidak --> G_EMPTY
        S_CHECK -- Ya --> S2["Render Kartu Hasil Pencarian"] --> G4
        G4 --> S3["6. Buka Detail Naskah (/workss/details.php)\nRender Abstrak & Generator Sitasi"]
        S3 --> G5
        G5 --> S4["8. Salin Teks Sitasi Baku ke Clipboard + Animasi Centang"]
        S4 --> G6
        G6 --> S5["10. Teruskan ke Script Streaming downloads.php\nValidasi Hak Akses Status Published\nInsert Entri Baru ke download_logs (IP, User-Agent, Waktu)\nUpdate download_count = download_count + 1\nStream Binary HTTP Content-Type: application/pdf"]
        S5 --> G7
    end
```
> **Tools:** StarUML v5.x / PlantUML | **File:** `docs/diagram/activity_pencarian_sitasi_unduh.puml`

#### Narasi Alur 6.4 (11 Langkah):
1. **Publik** membuka portal repositori Lentera melalui peramban web pada komputer atau ponsel pintar.
2. **Publik** memasukkan kata kunci penelitian pada input pencarian dan menerapkan filter spesifik (Fakultas, Program Studi, Jenis Dokumen, atau Rentang Tahun).
3. **Publik** menekan tombol *"Cari Naskah"*.
4. **Sistem** mengeksekusi *parameterized SQL query* dengan menyaring data naskah yang mutlak berstatus `'published'`.
5. Jika tidak ada data yang cocok, **Sistem** menyajikan tampilan *empty state* ramah pengguna beserta tombol atur ulang pencarian.
6. Jika naskah ditemukan, **Publik** mengklik judul naskah yang diinginkan, dan **Sistem** membuka halaman detail naskah (`details.php`).
7. **Sistem** menyajikan teks abstrak lengkap, metadata nama pembimbing, kata kunci, dan kotak generator sitasi otomatis.
8. **Publik** memilih format sitasi yang diinginkan (APA, IEEE, Harvard, atau BibTeX) lalu menekan tombol *"Salin Sitasi"*.
9. **Sistem** menyalin teks sitasi terstandar ke dalam *clipboard* sistem pengguna dengan konfirmasi visual animasi centang hijau.
10. **Publik** mengklik tombol *"Unduh Naskah PDF"* untuk mengunduh naskah lengkap.
11. **Sistem** memverifikasi status dokumen melalui skrip *indirect streaming* (`downloads.php`), mencatat rekam jejak audit (alamat IP, *browser user-agent*, timestamp) ke tabel `download_logs`, menambahkan hitungan `download_count + 1`, dan mengalirkan berkas biner PDF ke peramban pengguna.

---

### 5. Gambar 6.5 — Alur Manajemen Pengguna & Pengangkatan Peran Kurator

Diagram ini memodelkan proses tata kelola pengguna oleh Super Admin, khususnya verifikasi integritas dosen pembimbing dan pengangkatan hak akses (*Role Elevation*) dari peran Author menjadi Dewan Kurator.

```mermaid
flowchart TD
    subgraph Admin ["🛡️ Super Admin"]
        startNode((●)) --> AD1["1. Buka Menu Manajemen Pengguna (/admins/indexs.php)"]
        AD1 --> AD2["2. Cari Akun Dosen Berdasarkan Nama atau NIDN"]
        AD4["4. Verifikasi Data Dosen & Klik 'Ubah Hak Akses (Role)'"]
        AD4 --> AD5["5. Pilih Peran Baru: 'Kurator (Curator)'"]
        AD5 --> AD6["6. Tekan Tombol 'Simpan Perubahan Role'"]
    end

    subgraph Sistem ["⚙️ Sistem LENTERA"]
        AD2 --> SD1["3. Query Akun Pengguna & Tampilkan Profil Akun"] --> AD4
        AD6 --> SD2["7. Validasi Otorisasi Sesi Super Admin\nUpdate role = 'curator' pada Tabel users\nInsert Rekam Jejak ke Tabel activity_logs\nTampilkan Notifikasi Sukses"]
        SD2 --> USR1
        USR2 --> SD3["9. Auth Middleware Memeriksa Sesi Login\nMendeteksi Role = 'curator'\nRedirect Otomatis ke /reviews/indexs.php"]
        SD3 --> USR3
    end

    subgraph Dosen ["👨‍🏫 Dosen (Kurator Baru)"]
        USR1["8. Dosen Melakukan Login Masuk ke Sistem"]
        USR1 --> USR2["Input Kredensial Username & Password"]
        USR3["10. Masuk ke Ruang Kerja Antrean Kurasi Naskah"] --> endNode((◎))
    end
```
> **Tools:** StarUML v5.x / PlantUML | **File:** `docs/diagram/activity_manajemen_user_role.puml`

#### Narasi Alur 6.5 (9 Langkah):
1. **Super Admin** mengakses dasbor administrasi sistem pada tab *"Manajemen Pengguna"* (`/admins/indexs.php`).
2. **Super Admin** memasukkan nama lengkap atau nomor induk dosen (NIDN) pada kotak pencarian akun civitas.
3. **Sistem** menampilkan rincian akun yang cocok beserta status peran aktif saat ini (`author`).
4. **Super Admin** memverifikasi surat tugas akademik penelaah dan mengklik opsi *"Ubah Hak Akses / Role Elevation"*.
5. **Super Admin** memilih peran baru: *"Kurator (Curator)"* pada pilihan tingkat wewenang.
6. **Super Admin** menekan tombol *"Simpan Perubahan Role"*.
7. **Sistem** memverifikasi wewenang admin, memperbarui kolom `role = 'curator'` pada tabel `users`, dan mencatat mutasi hak akses ini ke dalam tabel `activity_logs`.
8. **Dosen** yang bersangkutan melakukan login ke portal Lentera menggunakan kredensial resminya.
9. **Sistem** melalui *role-based middleware* mendeteksi bahwa pengguna kini memegang peran `curator`, sehingga sistem secara otomatis mengalihkan navigasi langsung ke dasbor antrean kurasi naskah (`/reviews/indexs.php`) agar siap menelaah karya ilmiah civitas akademika.

---

<div align="center">
  <b>Sistem Repositori LENTERA — Universitas Madura (UNIRA)</b><br/>
  <sub>Disusun oleh Kelompok 01 (Imam, Alvin, Gilank, Halim) • PBP 2026</sub>
</div>

