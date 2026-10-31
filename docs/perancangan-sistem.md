# Dokumen Perancangan Sistem — LENTERA
## Repositori Karya Tulis Ilmiah Terpadu Akademik Universitas Madura (UNIRA)

Dokumen ini disusun sebagai acuan utama pengisian **BAB III (Perancangan Sistem)** pada berkas laporan resmi `Laporan-Kelompok-01-Lentera.docx`, memenuhi seluruh batasan dan aturan dosen pengampu:
- ✅ **Minimal 50 User Story** (US-01 s.d. US-50) lengkap dengan prioritas MoSCoW, estimasi story points, dan kriteria penerimaan (*acceptance criteria*).
- ✅ **Minimal 30 Use Case** yang merepresentasikan seluruh User Story dan dikelompokkan berdasarkan peran (*role*).
- ✅ **3 Skenario Detail Use Case** lengkap dengan *main flow*, *alternate flow*, dan aturan bisnis.
- ✅ **5 Narasi Activity Diagram** (7–12 langkah bernomor) untuk alur sistem tersulit.
- ✅ **Product Backlog (PBI)** dan **Sprint Backlog (Sprint 1, 2, 3)**.

---

## 1. User Story (50 User Story Terstruktur)

Format baku: **"Sebagai [peran], saya ingin [kebutuhan] sehingga [manfaat]"**  
Prioritas: **Must**, **Should**, **Could** (MoSCoW) | Estimasi: **1, 2, 3, 5, 8** story points.

### A. Peran: Publik (Guest / Pembaca) — [US-01 s.d. US-14]
| ID | User Story | Prioritas | Est. | Acceptance Criteria |
|:--:|---|:---:|:---:|---|
| **US-01** | Sebagai publik, saya ingin mengakses halaman beranda etalase tanpa login sehingga saya dapat melihat koleksi karya ilmiah kampus secara bebas. | Must | 2 | 1. Halaman beranda terbuka cepat tanpa meminta login.<br>2. Menampilkan ringkasan koleksi karya terbit. |
| **US-02** | Sebagai publik, saya ingin melihat daftar karya ilmiah terbit terbaru di beranda sehingga saya mengetahui riset akademik paling mutakhir. | Must | 3 | 1. Menampilkan maksimal 6–10 kartu naskah terbit terbaru.<br>2. Kartu memuat judul, penulis, tahun, dan tipe karya. |
| **US-03** | Sebagai publik, saya ingin mencari naskah menggunakan kata kunci judul sehingga saya cepat menemukan referensi yang spesifik. | Must | 3 | 1. Input kata kunci menghasilkan daftar naskah yang cocok pada judul.<br>2. Menampilkan pesan jika naskah tidak ditemukan. |
| **US-04** | Sebagai publik, saya ingin mencari naskah berdasarkan nama penulis sehingga saya dapat menelusuri karya dosen atau rekan mahasiswa tertentu. | Must | 3 | 1. Input nama penulis menampilkan seluruh naskah terbit milik penulis tersebut. |
| **US-05** | Sebagai publik, saya ingin menyaring karya ilmiah berdasarkan fakultas sehingga saya dapat fokus pada bidang ilmu yang relevan. | Must | 3 | 1. Filter dropdown/chip fakultas memperbarui hasil pencarian sesuai kode fakultas. |
| **US-06** | Sebagai publik, saya ingin menyaring karya berdasarkan tipe karya (skripsi, jurnal, tesis) sehingga saya menemukan format rujukan yang sesuai kebutuhan. | Should | 2 | 1. Pemilihan tipe karya hanya menampilkan naskah pada kategori tersebut. |
| **US-07** | Sebagai publik, saya ingin menyaring karya berdasarkan tahun publikasi sehingga saya mendapatkan data literatur terkini. | Should | 2 | 1. Filter tahun menyaring karya secara akurat sesuai kolom tahun terbit. |
| **US-08** | Sebagai publik, saya ingin membaca abstrak naskah lengkap pada halaman detail sehingga saya memahami intisari karya sebelum mengunduhnya. | Must | 2 | 1. Halaman detail menampilkan judul, abstrak lengkap, pembimbing, dan prodi. |
| **US-09** | Sebagai publik, saya ingin menyalin format sitasi APA 7th Edition secara otomatis sehingga saya tidak perlu menyusun sitasi manual. | Must | 2 | 1. Tersedia tombol salin sitasi APA.<br>2. Teks sitasi tersalin presisi ke clipboard. |
| **US-10** | Sebagai publik, saya ingin menyalin format sitasi IEEE dan Harvard sehingga karya dapat dikutip pada jurnal berstandar internasional. | Should | 2 | 1. Tersedia opsi tab format IEEE dan Harvard yang dapat disalin satu klik. |
| **US-11** | Sebagai publik, saya ingin mengunduh berkas lengkap PDF naskah yang berstatus terbit sehingga saya dapat mempelajari isi naskah secara utuh. | Must | 5 | 1. Tombol unduh mengirimkan berkas PDF naskah resmi.<br>2. Akses ditolak jika status naskah belum terbit. |
| **US-12** | Sebagai publik, saya ingin melihat jumlah keterbacaan (view count) dan unduhan naskah sehingga saya mengetahui popularitas riset tersebut. | Should | 1 | 1. Counter view dan download tampil secara realtime pada halaman detail naskah. |
| **US-13** | Sebagai publik, saya ingin melihat grafik statistik karya per fakultas sehingga saya mengetahui produktivitas riset institusi. | Should | 5 | 1. Halaman statistik menampilkan diagram batang/lingkaran Chart.js koleksi per fakultas. |
| **US-14** | Sebagai publik, saya ingin mendaftar akun penulis secara mandiri sehingga saya dapat mengajukan karya ilmiah saya ke repositori. | Must | 5 | 1. Form registrasi memvalidasi NIM, email, username unik, dan prodi.<br>2. Password di-hash aman. |

### B. Peran: Penulis (Author — Mahasiswa & Dosen) — [US-15 s.d. US-28]
| ID | User Story | Prioritas | Est. | Acceptance Criteria |
|:--:|---|:---:|:---:|---|
| **US-15** | Sebagai penulis, saya ingin masuk ke akun menggunakan email, username, atau NIM sehingga saya dapat mengakses ruang kerja pribadi saya. | Must | 3 | 1. Login berhasil jika identitas dan kata sandi cocok.<br>2. Menolak percobaan login dengan kredensial salah. |
| **US-16** | Sebagai penulis, saya ingin melihat dasbor karya pribadi saya sehingga saya dapat memantau status naskah yang pernah saya ajukan. | Must | 3 | 1. Dasbor menampilkan tabel naskah milik penulis yang login.<br>2. Menampilkan badge status kurasi terkini. |
| **US-17** | Sebagai penulis, saya ingin mengisi formulir pengajuan karya baru lengkap dengan metadata sehingga naskah saya masuk ke antrean kurasi. | Must | 5 | 1. Formulir memvalidasi judul, abstrak, prodi, pembimbing, dan jenis karya.<br>2. Dilindungi CSRF token. |
| **US-18** | Sebagai penulis, saya ingin mengunggah berkas naskah format PDF dengan batasan ukuran 20MB sehingga dokumen tersimpan aman di server. | Must | 5 | 1. Sistem memeriksa ekstensi file `.pdf` dan MIME type `application/pdf`.<br>2. Menolak berkas non-PDF atau melebihi 20MB. |
| **US-19** | Sebagai penulis, saya ingin karya saya otomatis berstatus `submitted` setelah diunggah sehingga naskah segera ditelaah oleh kurator. | Must | 2 | 1. Data naskah tersimpan di tabel `works` dengan status awal `submitted`. |
| **US-20** | Sebagai penulis, saya ingin melihat status naskah `in_review` ketika kurator sedang menelaah sehingga saya tahu progres pemeriksaan. | Should | 2 | 1. Dasbor menampilkan label "Sedang Ditelaah" saat kurator membuka naskah. |
| **US-21** | Sebagai penulis, saya ingin membaca catatan telaah dari kurator jika naskah berstatus `revision` sehingga saya tahu bagian yang harus diperbaiki. | Must | 3 | 1. Kotak catatan telaah kurator tampil jelas pada dasbor naskah penulis. |
| **US-22** | Sebagai penulis, saya ingin mengunggah berkas PDF revisi baru sehingga naskah masuk kembali ke antrean kurasi. | Must | 5 | 1. Formulir revisi menerima file PDF baru.<br>2. Status naskah kembali menjadi `submitted`. |
| **US-23** | Sebagai penulis, saya ingin riwayat revisi saya tersimpan bersiklus ($v_1 \rightarrow v_2$) sehingga rekam jejak perbaikan naskah tidak hilang. | Should | 5 | 1. Sistem mencatat versi naskah terdahulu di database dan penyimpanan server. |
| **US-24** | Sebagai penulis, saya ingin melihat halaman instruksi pembayaran publikasi saat naskah berstatus `waiting_payment` sehingga saya dapat menyelesaikan APC. | Should | 3 | 1. Menampilkan rincian biaya publikasi dan QRIS resmi institusi. |
| **US-25** | Sebagai penulis, saya ingin mengonfirmasi pembayaran mandiri sehingga status karya saya berpindah ke tahap publikasi resmi. | Should | 3 | 1. Tombol konfirmasi memperbarui status naskah menjadi `published` di etalase publik. |
| **US-26** | Sebagai penulis, saya ingin menerima pesan notifikasi/flash saat karya resmi terbit sehingga saya tahu naskah sudah bisa diakses publik. | Should | 2 | 1. Flash message sukses muncul di halaman dasbor penulis. |
| **US-27** | Sebagai penulis, saya ingin dapat keluar sesi (*logout*) secara aman sehingga akun saya tidak disalahgunakan di komputer bersama. | Must | 2 | 1. Sesi dihancurkan sepenuhnya saat logout.<br>2. Mengarahkan kembali ke halaman login. |
| **US-28** | Sebagai penulis, saya ingin mengakses karya saya melalui aplikasi mobile Flutter sehingga saya dapat mengecek status karya di mana saja. | Should | 5 | 1. Layar "Karya Saya" di mobile menampilkan naskah pribadi via Bearer token. |

### C. Peran: Kurator (Curator / Dewan Editor Mutu) — [US-29 s.d. US-40]
| ID | User Story | Prioritas | Est. | Acceptance Criteria |
|:--:|---|:---:|:---:|---|
| **US-29** | Sebagai kurator, saya ingin memiliki halaman antrean telaah khusus peran sehingga saya dapat memilah naskah yang menunggu kurasi. | Must | 3 | 1. Route `/reviews/indexs.php` dilindungi otorisasi peran `curator` dan `admin`. |
| **US-30** | Sebagai kurator, saya ingin melihat ringkasan antrean kurasi (total masuk, sedang ditelaah, revisi) sehingga pekerjaan terdistribusi rapi. | Should | 3 | 1. Dasbor menampilkan kartu ringkasan jumlah naskah per status kurasi. |
| **US-31** | Sebagai kurator, saya ingin membaca naskah dan mengunduh berkas draf PDF naskah penulis sehingga saya dapat menilai kelayakan isi. | Must | 3 | 1. Kurator dapat membuka file PDF naskah meskipun naskah belum berstatus terbit. |
| **US-32** | Sebagai kurator, saya ingin mengubah status naskah menjadi `in_review` sehingga penulis mengetahui naskahnya sedang diperiksa. | Should | 2 | 1. Aksi "Sedang Ditelaah" memperbarui status naskah di database. |
| **US-33** | Sebagai kurator, saya ingin memberikan catatan perbaikan dan mengubah status naskah menjadi `revision` sehingga penulis merevisi naskahnya. | Must | 3 | 1. Input komentar kurator wajib diisi jika memilih aksi revisi.<br>2. Catatan tersimpan di tabel `reviews`. |
| **US-34** | Sebagai kurator, saya ingin menyetujui penerbitan (*approve*) naskah yang layak sehingga karya langsung tampil di repositori publik. | Must | 3 | 1. Status naskah berubah menjadi `published`.<br>2. Kolom `published_at` terisi timestamp waktu persetujuan. |
| **US-35** | Sebagai kurator, saya ingin menolak (*reject*) naskah yang tidak memenuhi integritas akademik disertai alasan sehingga penulis memahaminya. | Must | 3 | 1. Status naskah berubah menjadi `rejected` dan catatan penolakan tersimpan. |
| **US-36** | Sebagai kurator, saya ingin mengalihkan naskah layak ke status `waiting_payment` sehingga penulis menyelesaikan administrasi publikasi sebelum terbit. | Should | 3 | 1. Status berubah menjadi `waiting_payment` sesuai migrasi ENUM 014. |
| **US-37** | Sebagai kurator, saya ingin menyaring antrean kurasi berdasarkan fakultas dan program studi sehingga penelaahan sesuai rumpun ilmu. | Should | 3 | 1. Filter prodi menyaring antrean telaah sesuai bidang keilmuan kurator. |
| **US-38** | Sebagai kurator, saya ingin melihat riwayat seluruh keputusan kurasi yang pernah saya berikan sehingga rekam jejak kerja saya terdokumentasi. | Should | 2 | 1. Menampilkan log riwayat telaah kurator pada tabel riwayat kurasi. |
| **US-39** | Sebagai kurator, saya ingin sistem memvalidasi sesi kurator sehingga sesi kedaluwarsa tidak memicu putusan ganda. | Must | 2 | 1. Form keputusan kurasi dilindungi token CSRF dan validasi sesi server-side. |
| **US-40** | Sebagai kurator, saya ingin melihat metrik akreditasi karya ilmiah kampus sehingga dewan kurator dapat mengevaluasi mutu berkala. | Could | 3 | 1. Akses langsung ke visualisasi analitik statistik repositori. |

### D. Peran: Super Admin (Pengelola IT Ops Repositori) — [US-41 s.d. US-50]
| ID | User Story | Prioritas | Est. | Acceptance Criteria |
|:--:|---|:---:|:---:|---|
| **US-41** | Sebagai admin, saya ingin memiliki panel kendali terpusat sehingga saya dapat mengawasi seluruh aktivitas repositori kampus. | Must | 5 | 1. Halaman admin menampilkan metrik pengguna, karya, dan log sistem secara terpusat. |
| **US-42** | Sebagai admin, saya ingin mengelola data akun pengguna (aktivasi/nonaktivasi) sehingga akun yang melanggar aturan dapat ditindak. | Must | 3 | 1. Admin dapat mengubah status aktif akun pengguna di tabel `users`. |
| **US-43** | Sebagai admin, saya ingin menetapkan peran kurator kepada dosen tertentu sehingga dosen memiliki wewenang telaah naskah. | Must | 3 | 1. Admin dapat mengubah role akun menjadi `curator` atau `author`. |
| **US-44** | Sebagai admin, saya ingin mengelola master data fakultas dan program studi sehingga struktur organisasi akademik selalu mutakhir. | Should | 3 | 1. Admin dapat melihat daftar prodi dan relasi fakultas di database. |
| **US-45** | Sebagai admin, saya ingin mengelola master jenis karya ilmiah sehingga kategori repositori dapat disesuaikan kebijakan universitas. | Should | 2 | 1. Tersedia relasi data tipe karya di tabel `work_types`. |
| **US-46** | Sebagai admin, saya ingin memantau log unduhan karya (IP, timestamp, user) sehingga kampus memiliki data otentik untuk borang akreditasi. | Must | 3 | 1. Menampilkan tabel audit log unduhan dari tabel `download_logs`. |
| **US-47** | Sebagai admin, saya ingin direktori naskah uploads terproteksi dari akses langsung URL sehingga PDF tidak bocor tanpa izin. | Must | 3 | 1. File `.htaccess` menolak akses langsung HTTP ke folder uploads. |
| **US-48** | Sebagai admin, saya ingin sistem memiliki penanganan galat terstandar (404/500) sehingga informasi sensitif server tidak bocor ke publik. | Must | 2 | 1. Mengembalikan tampilan galat resmi 404s/500s saat terjadi galat URL atau sistem. |
| **US-49** | Sebagai admin, saya ingin sistem menyediakan REST API terenkripsi token sehingga aplikasi mobile dapat terintegrasi aman. | Must | 5 | 1. Lapisan API di `/backend/api/` merespons JSON dengan token HMAC-SHA256. |
| **US-50** | Sebagai admin, saya ingin basis data mendukung migrasi non-destruktif sehingga pembaruan sistem tidak menghapus data naskah yang ada. | Must | 3 | 1. Berkas SQL migrasi (seperti `014_...sql`) dijalankan tanpa merusak tabel eksisting. |

---

## 2. Pemetaan 30 Use Case Sistem Lentera

Setiap use case merepresentasikan user story yang telah disusun di atas, dikelompokkan berdasarkan peran pengguna:

```
┌──────────────────────────────────────────────────────────────────────────────────┐
│                             USE CASE SISTEM LENTERA                              │
├──────────────────────────────────────────────────────────────────────────────────┤
│ 🌐 PUBLIK (GUEST)                                                                │
│  UC-01: Melihat Beranda Etalase & Metrik Koleksi      (US-01, US-02)             │
│  UC-02: Mencari Naskah via Kata Kunci & Penulis       (US-03, US-04)             │
│  UC-03: Menyaring Naskah per Fakultas & Prodi         (US-05, US-06, US-07)      │
│  UC-04: Menjelajahi Portal Fakultas                   (US-05)                    │
│  UC-05: Melihat Detail Naskah & Abstrak               (US-08, US-12)             │
│  UC-06: Menggunakan Generator Sitasi Otomatis         (US-09, US-10)             │
│  UC-07: Mengunduh Berkas Naskah PDF Resmi             (US-11, US-46)             │
│  UC-08: Mendaftar Akun Penulis Baru                   (US-14)                    │
├──────────────────────────────────────────────────────────────────────────────────┤
│ 📝 PENULIS (AUTHOR)                                                              │
│  UC-09: Masuk ke Akun (Login Web / Mobile API)        (US-15, US-49)             │
│  UC-10: Mengelola Dasbor Karya Pribadi                (US-16, US-28)             │
│  UC-11: Mengunggah Naskah Ilmiah Baru                 (US-17, US-18, US-19)      │
│  UC-12: Memantau Status & Catatan Telaah Kurator      (US-20, US-21)             │
│  UC-13: Mengunggah Dokumen Revisi Naskah              (US-22, US-23)             │
│  UC-14: Mengakses Formulir Pembayaran APC             (US-24)                    │
│  UC-15: Mengonfirmasi Pelunasan Publikasi             (US-25, US-26)             │
│  UC-16: Melihat Daftar Karya via Aplikasi Mobile      (US-28)                    │
│  UC-17: Keluar Sesi Akun (Logout Web / Mobile)        (US-27)                    │
│  UC-18: Mengubah Profil & Kata Sandi Penulis          (US-14, US-15)             │
├──────────────────────────────────────────────────────────────────────────────────┤
│ 🔍 KURATOR (CURATOR)                                                             │
│  UC-19: Mengakses Dasbor Antrean Kurasi Naskah        (US-29, US-30)             │
│  UC-20: Memeriksa Isi Berkas Naskah Masuk             (US-31)                    │
│  UC-21: Mengubah Status Naskah Menjadi In Review      (US-32)                    │
│  UC-22: Memberikan Catatan Telaah & Minta Revisi      (US-33)                    │
│  UC-23: Memberikan Persetujuan Publikasi (Approve)    (US-34)                    │
│  UC-24: Menolak Naskah Ilmiah (Reject)                (US-35)                    │
│  UC-25: Mengalihkan Naskah ke Status Menunggu Bayar   (US-36)                    │
│  UC-26: Memfilter Antrean Telaah per Rumpun Ilmu      (US-37)                    │
│  UC-27: Melihat Riwayat Keputusan Kurasi              (US-38, US-39)             │
├──────────────────────────────────────────────────────────────────────────────────┤
│ ⚙️ SUPER ADMIN (IT OPS)                                                          │
│  UC-28: Mengelola Data Pengguna & Hak Akses Peran     (US-41, US-42, US-43)      │
│  UC-29: Mengelola Master Data Fakultas & Prodi        (US-44, US-45)             │
│  UC-30: Memantau Audit Log Unduhan & Analitik Kampus  (US-40, US-46, US-47)      │
└──────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Skenario Detail Use Case (3 Skenario Kunci)

### Skenario 1: UC-11 — Mengunggah Naskah Ilmiah Baru
| Field | Isi Deskripsi |
|---|---|
| **ID + Nama Use Case** | **UC-11: Mengunggah Naskah Ilmiah Baru** |
| **Aktor Primer & Tujuan** | Penulis (Author); bertujuan memasukkan karya ilmiah dan file PDF naskah ke antrean repositori Lentera. |
| **Precondition** | Penulis telah terdaftar dan login ke sistem (sesi valid). |
| **Postcondition** | Naskah tersimpan di basis data dengan status `submitted`, berkas PDF tersimpan terproteksi di folder `uploads/works/`. |
| **Main Flow (Alur Utama)** | 1. Penulis membuka halaman Unggah Naskah (`viewss/workss/creates.php`).<br>2. Sistem menampilkan formulir pengunggahan beserta token CSRF.<br>3. Penulis mengisi judul, abstrak naskah, dosen pembimbing, memilih prodi dan jenis karya.<br>4. Penulis memilih berkas PDF naskah dari penyimpanan lokal.<br>5. Penulis menekan tombol "Unggah Karya".<br>6. Sistem memvalidasi seluruh kolom dan memeriksa format berkas (PDF, MIME `application/pdf`, ukuran $\le$ 20MB).<br>7. Sistem menyimpan metadata naskah dengan status `submitted` dan menyimpan fisik PDF dengan nama acak unik.<br>8. Sistem mengarahkan penulis ke Dasbor Karya dengan pesan sukses. |
| **Alternate Flow** | 4a. Jika file yang diunggah bukan PDF atau ukuran $>$ 20MB:<br>&nbsp;&nbsp;&nbsp;&nbsp;1. Sistem menolak berkas, proses penyimpanan dibatalkan.<br>&nbsp;&nbsp;&nbsp;&nbsp;2. Sistem menampilkan pesan galat: "Berkas harus format PDF dan ukuran maksimal 20MB".<br>6a. Jika token CSRF tidak valid atau sesi kedaluwarsa:<br>&nbsp;&nbsp;&nbsp;&nbsp;1. Sistem menolak permintaan dan meminta penulis memuat ulang formulir. |
| **Aturan Bisnis** | Naskah baru tidak boleh langsung tampil ke publik (*no direct auto-publish*); naskah wajib melalui antrean kurasi kurator. |

---

### Skenario 2: UC-22 — Memberikan Catatan Telaah & Meminta Revisi
| Field | Isi Deskripsi |
|---|---|
| **ID + Nama Use Case** | **UC-22: Memberikan Catatan Telaah & Minta Revisi** |
| **Aktor Primer & Tujuan** | Kurator (Curator); bertujuan mengembalikan naskah kepada penulis untuk diperbaiki berdasarkan telaah mutu. |
| **Precondition** | Kurator telah login; naskah berada pada status `submitted` atau `in_review`. |
| **Postcondition** | Status naskah berubah menjadi `revision`, catatan tersimpan di tabel `reviews`, penulis dapat membaca catatan revisi. |
| **Main Flow (Alur Utama)** | 1. Kurator membuka Dasbor Kurasi (`viewss/reviews/indexs.php`).<br>2. Kurator memilih salah satu naskah pada antrean telaah.<br>3. Sistem menampilkan detail naskah, abstrak, dan tautan tinjau naskah PDF.<br>4. Kurator membaca dokumen dan menemukan perbaikan format atau literatur rujukan.<br>5. Kurator memilih aksi keputusan: "Minta Revisi".<br>6. Kurator menuliskan catatan perbaikan substantif pada kolom komentar.<br>7. Kurator mengirimkan keputusan telaah.<br>8. Sistem memvalidasi komentar tidak boleh kosong dan menyimpan catatan ke tabel `reviews`.<br>9. Sistem memperbarui status naskah di tabel `works` menjadi `revision`.<br>10. Sistem menampilkan konfirmasi bahwa catatan revisi berhasil dikirim ke penulis. |
| **Alternate Flow** | 6a. Jika kurator mengosongkan kolom catatan telaah:<br>&nbsp;&nbsp;&nbsp;&nbsp;1. Sistem menampilkan peringatan: "Catatan telaah wajib diisi saat meminta revisi".<br>&nbsp;&nbsp;&nbsp;&nbsp;2. Status naskah tidak berubah hingga catatan dilengkapi. |
| **Aturan Bisnis** | Keputusan revisi hanya dapat dilakukan oleh kurator atau admin resmi yang memiliki wewenang penelaahan. |

---

### Skenario 3: UC-07 — Mengunduh Berkas Naskah PDF Resmi
| Field | Isi Deskripsi |
|---|---|
| **ID + Nama Use Case** | **UC-07: Mengunduh Berkas Naskah PDF Resmi** |
| **Aktor Primer & Tujuan** | Publik atau Peneliti; bertujuan mengunduh berkas lengkap naskah ilmiah yang telah terbit. |
| **Precondition** | Naskah telah berstatus `published` atau pengunduh adalah pemilik naskah / kurator. |
| **Postcondition** | Berkas PDF terkirim ke peramban pengguna; counter `download_count` bertambah dan log tercatat di `download_logs`. |
| **Main Flow (Alur Utama)** | 1. Pengguna membuka halaman detail naskah (`viewss/workss/details.php?ids=X`).<br>2. Pengguna menekan tombol "Unduh PDF Naskah".<br>3. Permintaan diarahkan ke pengontrol unduhan (`viewss/workss/downloads.php?ids=X`).<br>4. Sistem memeriksa status naskah di basis data.<br>5. Sistem memverifikasi status karya adalah `published`.<br>6. Sistem mencatat alamat IP pengunduh, user ID (jika login), dan timestamp ke tabel `download_logs`.<br>7. Sistem menambahkan nilai `download_count` pada tabel `works`.<br>8. Sistem mengirimkan header HTTP (`application/pdf; attachment`) dan mengalirkan berkas fisik PDF ke peramban pengguna. |
| **Alternate Flow** | 5a. Jika naskah berstatus `draft`, `submitted`, `in_review`, atau `rejected` dan pengunduh bukan pemilik/staf:<br>&nbsp;&nbsp;&nbsp;&nbsp;1. Sistem menghentikan proses pengunduhan.<br>&nbsp;&nbsp;&nbsp;&nbsp;2. Sistem mengembalikan respons HTTP 403 Forbidden: "Akses ditolak: Berkas hanya dapat diunduh jika karya telah berstatus terbit". |
| **Aturan Bisnis** | Folder penyimpanan PDF dilindungi di tingkat web server; dilarang keras mengizinkan unduhan langsung via path URL fisik. |

---

## 4. Narasi 5 Activity Diagram (Alur Sistem Tersulit)

### Alur 1: Pengajuan Naskah Karya Ilmiah Baru (Penulis $\rightarrow$ Sistem)
1. Penulis membuka halaman pengajuan karya ilmiah pada peramban web.
2. Sistem memeriksa sesi autentikasi dan memuat formulir unggah beserta token proteksi CSRF.
3. Penulis menginput data judul naskah, abstrak, nama dosen pembimbing, serta memilih program studi dan kategori karya.
4. Penulis memilih berkas naskah format PDF dari penyimpanan komputer.
5. Penulis mengirimkan formulir (*submit*).
6. Sistem memverifikasi integritas token CSRF formulir.
7. Sistem memvalidasi kelengkapan teks masukan dan memeriksa validitas tipe MIME dan ekstensi berkas PDF ($\le$ 20MB).
8. Jika berkas tidak valid, sistem menampilkan pesan galat dan mengembalikan pengguna ke formulir.
9. Jika valid, sistem mengenkripsi/mengacak nama berkas fisik dan menyimpannya ke direktori terproteksi `uploads/works/`.
10. Sistem menyimpan record naskah baru ke tabel `works` dengan status awal `submitted`.
11. Sistem mencatat log aktivitas pengajuan dan mengalihkan penulis ke dasbor karya pribadi disertai notifikasi sukses.

### Alur 2: Penelaahan & Putusan Kurasi (Kurator $\rightarrow$ Sistem $\rightarrow$ Penulis)
1. Kurator mengakses Dasbor Kurasi dan sistem menampilkan antrean naskah yang menunggu telaah.
2. Kurator memilih salah satu naskah masuk untuk ditelaah.
3. Sistem menyajikan ringkasan metadata, abstrak, dan tautan galley PDF draf.
4. Kurator membuka berkas PDF dan membaca keseluruhan naskah untuk menguji mutu dan format akademik.
5. Kurator memilih salah satu opsi putusan editorial: *Approve* (Terbit), *Revision* (Minta Revisi), *Waiting Payment* (Menunggu Pembayaran), atau *Reject* (Tolak).
6. Kurator menuliskan catatan telaah pada kolom komentar keputusan.
7. Kurator mengonfirmasi pengiriman keputusan kurasi.
8. Sistem memvalidasi bahwa komentar tidak kosong jika aksi yang dipilih adalah revisi atau penolakan.
9. Sistem menyimpan catatan telaah ke tabel `reviews` dengan merekam ID kurator penelaah.
10. Sistem memperbarui status naskah di tabel `works` sesuai putusan kurator.
11. Jika disetujui (*Approve*), sistem mengisi kolom `published_at` sehingga naskah langsung tayang di katalog publik.
12. Sistem memperbarui antrean kurasi dan mengirimkan status terbaru ke dasbor karya penulis.

### Alur 3: Pengunggahan Revisi Naskah Berversi (Penulis $\rightarrow$ Sistem $\rightarrow$ Kurator)
1. Penulis membuka dasbor naskah pribadi dan melihat naskah bertanda status `revision`.
2. Penulis mengklik tombol "Unggah Revisi" untuk membuka formulir revisi naskah.
3. Sistem membaca catatan telaah dari tabel `reviews` dan menampilkannya di bagian atas formulir.
4. Penulis membaca masukan kurator dan menyiapkan dokumen PDF naskah hasil revisi.
5. Penulis mengunggah berkas PDF revisi baru dan menekan tombol kirim revisi.
6. Sistem memvalidasi ekstensi `.pdf` dan ukuran berkas revisi.
7. Sistem mengarsipkan versi berkas naskah terdahulu ke riwayat versi.
8. Sistem menyimpan berkas PDF versi terbaru ke direktori server.
9. Sistem memperbarui status naskah dari `revision` kembali menjadi `submitted`.
10. Sistem mencatat waktu revisi pada kolom `updated_at`.
11. Sistem memperbarui dasbor penulis dan memasukkan naskah kembali ke antrean kurasi kurator.

### Alur 4: Pencarian Katalog, Detail & Sitasi Otomatis (Publik $\rightarrow$ Sistem)
1. Pengunjung/pembaca membuka halaman katalog pencarian repositori Lentera.
2. Pengunjung memasukkan kata kunci pada kotak pencarian dan/atau memilih filter fakultas.
3. Pengunjung menekan tombol cari.
4. Sistem mengeksekusi prepared query SQL menyaring naskah yang berstatus `published` berdasarkan kesesuaian judul, abstrak, penulis, atau fakultas.
5. Sistem menyajikan kartu hasil pencarian lengkap dengan informasi judul, penulis, tahun, dan jumlah unduhan.
6. Pengunjung mengklik salah satu kartu karya untuk membaca detail lengkap.
7. Sistem mengambil record naskah, menambahkan nilai `view_count` (+1), dan menampilkan abstrak lengkap.
8. Sistem memicu generator sitasi otomatis untuk menyusun teks rujukan format APA 7th, IEEE, dan Harvard.
9. Pengunjung memilih format sitasi yang diinginkan dan mengklik tombol "Salin Sitasi".
10. Sistem menyalin teks sitasi terformat ke *clipboard* pengguna dan memunculkan notifikasi konfirmasi salin.

### Alur 5: Unduh Naskah Terproteksi & Pencatatan Audit Log (Publik $\rightarrow$ Sistem)
1. Pengguna mengklik tombol "Unduh PDF Naskah" pada halaman detail karya ilmiah.
2. Peramban mengirimkan permintaan HTTP GET ke endpoint `viewss/workss/downloads.php?ids=X`.
3. Skrip controller unduhan menginisialisasi sesi dan memeriksa parameter ID naskah.
4. Sistem memverifikasi keberadaan data naskah pada tabel `works`.
5. Sistem memeriksa apakah status naskah adalah `published`, atau apakah pengguna login adalah penulis naskah tersebut / kurator.
6. Jika status bukan terbit dan pengguna tidak berhak, sistem menghentikan proses dan memunculkan galat 403 Forbidden.
7. Jika berhak, sistem memverifikasi keberadaan fisik berkas PDF pada penyimpanan server.
8. Sistem mencatat audit log unduhan ke tabel `download_logs` (ID karya, ID pengunduh, alamat IP, waktu unduh).
9. Sistem mengeksekusi pembaruan nilai `download_count = download_count + 1` pada tabel `works`.
10. Sistem mengirimkan header HTTP transfer berkas biner PDF ke peramban.
11. Berkas PDF resmi institusi terunduh ke perangkat pengguna secara utuh.

---

## 5. Product Backlog & Sprint Backlog

### A. Product Backlog (PBI)
| ID | Deskripsi Fitur / Kebutuhan | Points | Sprint | Status | Kriteria Selesai (Definition of Done) |
|:--:|---|:---:|:---:|:---:|---|
| **PBI-01** | Fondasi Arsitektur MVC, Database PDO Prepared Statements & Auto-Healing | 8 | S1 | Selesai | Basis data `db_lentera` terinisialisasi otomatis tanpa SQL injection. |
| **PBI-02** | Pengamanan Repositori (.gitignore, .htaccess uploads, sanitasi kredensial) | 5 | S1 | Selesai | Berkas uploads terproteksi deny all; kredensial tidak bocor di git. |
| **PBI-03** | Modul Autentikasi Pengguna & Registrasi Mandiri Mahasiswa | 5 | S1 | Selesai | Registrasi validasi NIM/email, hash password bcrypt, sesi secure. |
| **PBI-04** | Modul Pengajuan Naskah & Pengunggahan Berkas PDF Penulis | 8 | S2 | Selesai | Form upload memvalidasi PDF $\le$ 20MB, status naskah `submitted`. |
| **PBI-05** | Modul Kurasi & Dewan Editorial (Approve, Revision, Reject, Pay) | 8 | S2 | Selesai | Kurator dapat memberi catatan telaah dan mengubah status naskah. |
| **PBI-06** | Modul Siklus Revisi Naskah Berversi Penulis | 5 | S2 | Selesai | Penulis dapat membaca catatan telaah kurator dan mengunggah revisi baru. |
| **PBI-07** | Katalog Publik, Pencarian Multi-Filter & Portal per Fakultas | 8 | S2 | Selesai | Pencarian judul/penulis/fakultas berjalan cepat tanpa kewajiban login. |
| **PBI-08** | Generator Sitasi Otomatis (APA 7th, IEEE, Harvard) & Audit Unduhan | 5 | S2 | Selesai | Generator sitasi satu-klik; unduhan mencatat audit log IP dan waktu. |
| **PBI-09** | Dasbor Analitik Statistik Akreditasi & Panel Administrasi IT Ops | 5 | S2 | Selesai | Chart.js visualisasi koleksi; admin dapat mengelola user & prodi. |
| **PBI-10** | REST API JSON Terpadu (Auth, Recents, Search, Details, Authors) | 8 | S3 | Selesai | 6 Endpoint API merespons JSON standar dengan token HMAC-SHA256. |
| **PBI-11** | Aplikasi Mobile Native Flutter (Home, Search, Detail, Citations) | 8 | S3 | Selesai | UI Flutter responsif, graceful offline handling, safe area notch. |
| **PBI-12** | Autentikasi & Dasbor Mobile Penulis (Login Token & Karya Saya) | 5 | S3 | Selesai | Penulis dapat login di mobile dan melihat daftar karya pribadinya. |

### B. Sprint Backlog

#### Sprint 1 — Goal: Fondasi Arsitektur, Keamanan & Autentikasi Sistem
*Periode: Minggu 1–2 | Velocity Rencana: 18 Story Points | Status: Selesai*
| Task ID | Task Teknis | Dari PBI | Pemilik | Est. (Jam) | Status |
|:--:|---|:---:|---|:---:|:---:|
| **S1-T01** | Setup skema basis data 14 tabel migrasi dan seeder awal UNIRA | PBI-01 | Imam | 6 | Selesai |
| **S1-T02** | Implementasi PDO Singleton `databases.php` dengan fitur auto-healing | PBI-01 | Imam | 5 | Selesai |
| **S1-T03** | Konfigurasi `.gitignore` resmi dosen dan pengamanan folder `uploads/` | PBI-02 | Imam | 4 | Selesai |
| **S1-T04** | Pembuatan helper keamanan CSRF, Session Manager, dan Validasi Input | PBI-02 | Imam | 6 | Selesai |
| **S1-T05** | Pembuatan formulir registrasi mandiri mahasiswa & hashing password | PBI-03 | Alvin | 7 | Selesai |
| **S1-T06** | Pembuatan formulir login berbasis peran (Author, Curator, Admin) | PBI-03 | Alvin | 6 | Selesai |

#### Sprint 2 — Goal: Siklus Naskah, Kurasi Mutu, Katalog Publik & Analitik
*Periode: Minggu 3–4 | Velocity Rencana: 34 Story Points | Status: Selesai*
| Task ID | Task Teknis | Dari PBI | Pemilik | Est. (Jam) | Status |
|:--:|---|:---:|---|:---:|:---:|
| **S2-T01** | Pembuatan antarmuka & kontroler pengajuan naskah ilmiah baru | PBI-04 | Imam / Alvin | 8 | Selesai |
| **S2-T02** | Validasi berkas PDF upload (MIME, ekstensi, batas ukuran 20MB) | PBI-04 | Imam | 4 | Selesai |
| **S2-T03** | Pembuatan dasbor kurasi mutu & antrean telaah editorial kurator | PBI-05 | Imam / Alvin | 9 | Selesai |
| **S2-T04** | Implementasi formulir keputusan kurator (terbit, revisi, tolak) | PBI-05 | Imam | 7 | Selesai |
| **S2-T05** | Pembuatan alur upload revisi naskah berversi bagi penulis | PBI-06 | Alvin | 6 | Selesai |
| **S2-T06** | Pembuatan halaman muka beranda etalase & portal khusus fakultas | PBI-07 | Alvin | 8 | Selesai |
| **S2-T07** | Implementasi mesin pencari katalog dengan multi-filter fakultas | PBI-07 | Alvin | 7 | Selesai |
| **S2-T08** | Pembuatan generator sitasi otomatis (APA, IEEE, Harvard) & audit unduhan | PBI-08 | Alvin / Imam | 6 | Selesai |
| **S2-T09** | Integrasi visualisasi Chart.js statistik koleksi akreditasi kampus | PBI-09 | Alvin | 6 | Selesai |
| **S2-T10** | Pembuatan panel kendali Super Admin (manajemen user, prodi, log) | PBI-09 | Imam | 8 | Selesai |

#### Sprint 3 — Goal: Integrasi Multiplatform Mobile Native Flutter & REST API
*Periode: Minggu 5–6 | Velocity Rencana: 21 Story Points | Status: Selesai*
| Task ID | Task Teknis | Dari PBI | Pemilik | Est. (Jam) | Status |
|:--:|---|:---:|---|:---:|:---:|
| **S3-T01** | Perancangan & implementasi 6 endpoint REST API JSON di `/backend/api/` | PBI-10 | Imam | 10 | Selesai |
| **S3-T02** | Implementasi generator & verifikator token mobile HMAC-SHA256 | PBI-10 | Imam | 5 | Selesai |
| **S3-T03** | Inisialisasi arsitektur proyek Flutter, pubspec.yaml, dan konfigurasi tema | PBI-11 | Gilank | 5 | Selesai |
| **S3-T04** | Pembuatan model data Flutter (`WorkModel`, `UserModel`, `FacultyModel`) | PBI-11 | Gilank | 4 | Selesai |
| **S3-T05** | Pembangunan lapisan layanan jaringan `ApiService` dengan offline handling | PBI-11 | Gilank | 7 | Selesai |
| **S3-T06** | Pembuatan layar Beranda Mobile, Quick Search, dan Filter Fakultas | PBI-11 | Gilank | 8 | Selesai |
| **S3-T07** | Pembuatan layar Detail Naskah & Generator Sitasi Clipboard Mobile | PBI-11 | Gilank | 6 | Selesai |
| **S3-T08** | Pembuatan layar Login Mobile dengan penyimpanan sesi token | PBI-12 | Gilank | 6 | Selesai |
| **S3-T09** | Pembuatan layar Dasbor Karya Saya dengan badge status kurasi dinamis | PBI-12 | Gilank | 6 | Selesai |
| **S3-T10** | Pengujian widget tree, kepatuhan safe area notch, dan dokumentasi README | PBI-11/12 | Gilank / Halim | 5 | Selesai |
