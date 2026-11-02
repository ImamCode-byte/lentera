# Proposal Proyek Akhir — LENTERA
## Repositori Karya Tulis Ilmiah Terpadu Akademik Universitas Madura (UNIRA)

**Mata Kuliah:** Pemrograman Berbasis Platform  
**Program Studi:** Teknik Informatika — Fakultas Teknik  
**Perguruan Tinggi:** Universitas Madura (UNIRA)  
**Kelompok:** 01 — Kelas TI 6A (2025/2026 Genap)  

| Kolom Sampul | Data Kelompok |
|---|---|
| **Judul Sistem** | LENTERA — Laman Etalase Karya Tulis Ilmiah Terpadu Akademik Universitas Madura |
| **Kelompok / Kelas** | Kelompok 01 — TI 6A (Pagi) |
| **Anggota + NIM + Peran** | 1. Imam (2024520030) — Ketua, Backend Lead & Keamanan (`imamdhespachito001@gmail.com`)<br>2. Alvin Hidayat Putra Pratam (2024520050) — Frontend Web Engineer (`alvnhdytptr@gmail.com`)<br>3. Moh. Gilank Alamsyah (2024520052) — Mobile Native Engineer (`alamsyahgilank@gmail.com`)<br>4. Abdul Halim (2024520059) — DB Engineer & QA Lead (`kikybagaskara@gmail.com`) |
| **Dosen Pengampu** | Dosen Pengampu Pemrograman Berbasis Platform |
| **Tahun / Semester** | 2025/2026 Genap |
| **Tautan Repositori** | `https://github.com/ImamCode-byte/LENTERA` |
| **Tautan API / Demo** | `http://localhost/lentera/` (Lokal) / `http://[IP_SERVER]/lentera/` |

---

## BAB I: PENDAHULUAN

### 1.1 Latar Belakang

Perguruan tinggi merupakan pusat gravitasi intelektual yang menghasilkan luaran akademik berlimpah setiap tahun ajaran, meliputi skripsi program sarjana, tesis program magister, naskah jurnal terakreditasi, laporan kerja praktik, serta laporan pengabdian masyarakat. Karya-karya tersebut adalah aset pengetahuan dan memori institusional civitas akademika yang bernilai strategis bagi kemajuan ilmu pengetahuan dan teknologi.

Dalam kondisi ideal yang diharapkan, seluruh naskah karya ilmiah civitas akademika Universitas Madura (UNIRA) terarsip secara digital, tersusun rapi dalam skema metadata terstandar, melalui gerbang penelaahan kualitas mutu (*curation workflow*), serta dapat ditelusuri dan diakses dengan cepat oleh mahasiswa, dosen, maupun masyarakat luas sebagai referensi riset ilmiah.

Namun pada kenyataannya, kondisi aktual di lapangan menunjukkan bahwa pengelolaan arsip karya ilmiah masih terfragmentasi dan belum terintegrasi secara digital. Ratusan skripsi fisik menumpuk di lemari arsip program studi tanpa keterbacaan yang optimal, sementara file digital naskah hanya tersimpan pada *flashdisk* pribadi mahasiswa yang telah lulus atau tercecer di penyimpanan komputasi awan pribadi dosen pembimbing. Akibatnya, adik tingkat dan peneliti pemula mengalami hambatan besar dalam mencari literatur rujukan terdahulu, sehingga memicu risiko duplikasi topik riset akademik yang tidak disengaja.

Kesenjangan tersebut berdampak nyata saat periode evaluasi akreditasi program studi tiba, di mana staf pengelola fakultas kelimpungan mengumpulkan data fisik luaran penelitian mahasiswa dan rekam jejak pembimbingan dalam tenggat waktu yang sangat sempit. Ketiadaan pencatatan log akses dan statistik unduhan juga membuat kampus kehilangan metrik objektif mengenai karya ilmiah mana yang paling berdampak dan banyak dijadikan referensi. Faktor mendasar dari permasalahan ini adalah belum tersedianya platform repositori digital terpusat yang menjembatani siklus hidup naskah ilmiah kampus dari tahap pengajuan (*submission*), penelaahan mutu kurator (*peer curation*), hingga publikasi bebas (*open access*).

Menjawab urgensi tersebut, diperlukan sebuah platform repositori akademik multiplatform bernama **LENTERA** (*Laman Etalase Karya Tulis Ilmiah Terpadu Akademik*). LENTERA dirancang dengan arsitektur web responsif dan aplikasi mobile native (Flutter) yang menghubungkan tiga penghuni repositori (*The Triad Actors*): Penulis yang mengajukan karya, Kurator yang menjaga mutu telaah, dan Publik yang menelusuri katalog ilmiah kampus secara cepat dan terbuka.

---

### 1.2 Rumusan Masalah

Berdasarkan latar belakang di atas, rumusan masalah dalam pengembangan sistem Lentera adalah:
1. **[M-01]** Bagaimana merancang arsitektur basis data relasional dan sistem unggah berkas yang mampu mengamankan penyimpanan dokumen PDF serta mencatat metadata karya secara terstruktur?
2. **[M-02]** Bagaimana membangun mekanisme penelaahan editorial bertingkat (*curation cycle*) dengan siklus revisi berversi ($v_1, v_2$) sebelum karya ilmiah resmi diterbitkan ke publik?
3. **[M-03]** Bagaimana mengimplementasikan mesin pencari multi-kriteria berbasis judul, abstrak, penulis, dan fakultas yang responsif pada antarmuka web?
4. **[M-04]** Bagaimana menyediakan generator sitasi multi-format standar akademik (APA 7th, IEEE, Harvard) untuk mempermudah periset mengutip karya ilmiah?
5. **[M-05]** Bagaimana merancang antarmuka mobile native Android & iOS (Flutter) dengan integrasi REST API yang tangguh menghadapi kondisi jaringan tanpa internet (*offline handling*)?

---

### 1.3 Solusi Digital (Fitur Web & Mobile — Minimal 12 Fitur per Platform)

Berikut adalah pemetaan solusi digital sistem Lentera yang mencakup **14 Fitur Web** dan **14 Fitur Mobile** serta **4 Role Pengguna** (Penulis, Kurator, Publik, Super Admin):

#### A. Tabel Fitur Web (14 Fitur)
| ID Fitur | Nama Fitur Web | Menjawab Masalah | MVP / Lanjutan | Pengguna |
|:---:|---|:---:|:---:|---|
| **FW-01** | Beranda Etalase & Metrik Koleksi Kampus | M-03 | MVP | Publik, Penulis, Kurator |
| **FW-02** | Registrasi Mandiri Akun Peneliti/Mahasiswa | M-01 | MVP | Publik / Mahasiswa |
| **FW-03** | Autentikasi Login Berbasis Peran (RBAC) | M-01 | MVP | Penulis, Kurator, Admin |
| **FW-04** | Formulir Pengunggahan Naskah & Berkas PDF | M-01 | MVP | Penulis |
| **FW-05** | Dasbor Karya Penulis & Pelacakan Status | M-02 | MVP | Penulis |
| **FW-06** | Formulir Unggah Dokumen Revisi Siklus Berversi | M-02 | MVP | Penulis |
| **FW-07** | Dasbor Kurasi & Antrean Naskah Masuk | M-02 | MVP | Kurator, Admin |
| **FW-08** | Formulir Keputusan Kurator (Terbit, Revisi, Tolak) | M-02 | MVP | Kurator, Admin |
| **FW-09** | Portal Repositori Khusus per Fakultas | M-03 | MVP | Publik |
| **FW-10** | Pencarian Multi-Filter Katalog Ilmiah | M-03 | MVP | Publik |
| **FW-11** | Halaman Detail Naskah & Generator Sitasi Otomatis | M-04 | MVP | Publik |
| **FW-12** | Pengunduh PDF Terproteksi dengan Audit Log | M-01 | MVP | Publik, Penulis |
| **FW-13** | Dasbor Analitik Statistik & Borang Akreditasi | M-01 | Lanjutan | Publik, Kurator, Admin |
| **FW-14** | Panel Super Admin (Kelola User, Prodi & Log) | M-01 | Lanjutan | Super Admin |

#### B. Tabel Fitur Mobile Native (14 Fitur)
| ID Fitur | Nama Fitur Mobile | Menjawab Masalah | MVP / Lanjutan | Pengguna |
|:---:|---|:---:|:---:|---|
| **FM-01** | Beranda Mobile & Quick Search Bar | M-05 | MVP | Publik |
| **FM-02** | Chip Filter Cepat Koleksi per Fakultas | M-05 | MVP | Publik |
| **FM-03** | Pencarian Katalog Realtime Naskah Ilmiah | M-03, M-05 | MVP | Publik |
| **FM-04** | Filter Dropdown Fakultas di Layar Pencarian | M-03, M-05 | MVP | Publik |
| **FM-05** | Layar Detail Lengkap Naskah & Abstrak Ilmiah | M-05 | MVP | Publik |
| **FM-06** | Generator Sitasi Otomatis Multi-Style (APA/IEEE/Harvard) | M-04, M-05 | MVP | Publik |
| **FM-07** | Salin Format Sitasi ke Clipboard Satu-Sentuhan | M-04, M-05 | MVP | Publik |
| **FM-08** | Formulir Login Mobile Aman Berbasis REST API | M-01, M-05 | MVP | Penulis, Kurator |
| **FM-09** | Manajemen Sesi Token (Persistent Secure Storage) | M-01, M-05 | MVP | Penulis, Kurator |
| **FM-10** | Dasbor Karya Saya (Status Naskah Penulis) | M-02, M-05 | MVP | Penulis |
| **FM-11** | Badge Status Kurasi Dinamis (Warna & Label Baku) | M-02, M-05 | MVP | Penulis |
| **FM-12** | Penanganan Status Offline Adaptif & Tombol Retry | M-05 | MVP | Semua Pengguna |
| **FM-13** | Komponen Empty State & Loading Skeleton | M-05 | MVP | Semua Pengguna |
| **FM-14** | Tautan Akses Unduhan Berkas PDF Resmi Kampus | M-01, M-05 | Lanjutan | Publik, Penulis |

---

## 4. Alasan Pemilihan Topik

1. **Relevansi Nyata dengan Kebutuhan Kampus UNIRA**: Repositori karya ilmiah menjawab permasalahan nyata ketersebaran dokumen dan pelaporan akreditasi perguruan tinggi.
2. **Kesesuaian Capaian Mata Kuliah PBP**: Menerapkan konsep arsitektur MVC server-side, integrasi REST API JSON, database prepared statements, keamanan web standar OWASP, serta pengembangan aplikasi multiplatform modern (Flutter).
3. **Kesiapan Implementasi Berkelanjutan**: Kode sumber dibangun dengan standar modular tinggi sehingga siap diadopsi secara resmi oleh perpustakaan dan universitas.
