# Backlog Pengerjaan Proyek — LENTERA (Kelompok 01)

Dokumen manajemen tugas dan pelacakan progres pengembangan platform repositori Lentera.

---

## Matriks Backlog Tugas

| ID | Modul / Pekerjaan | Prioritas | Penanggung Jawab | Status | Tautan Berkas / Fitur |
|:--:|---|:--:|:--:|:--:|---|
| **BK-01** | Audit keamanan awal & perlindungan `.env` dari Git | 🔴 Tinggi | Imam | ✅ Selesai | `.gitignore`, `.env.example` |
| **BK-02** | Proteksi akses langsung direktori uploads via `.htaccess` | 🔴 Tinggi | Imam | ✅ Selesai | `uploads/works/.htaccess` |
| **BK-03** | Validasi status naskah pada unduhan `downloads.php` | 🔴 Tinggi | Imam | ✅ Selesai | `frontend/web/viewss/workss/downloads.php` |
| **BK-04** | Migrasi non-destruktif ENUM `waiting_payment` & `acc_payment` | 🔴 Tinggi | Imam | ⏳ Menunggu Uji DB | `backend/databases/migrationss/014_...` |
| **BK-05** | Penyesuaian struktur repository resmi dosen (`git mv`) | 🔴 Tinggi | Imam | ✅ Selesai | `backend/`, `frontend/`, `docs/` |
| **BK-06** | Penyusunan template GitHub Issues & Pull Request | 🟡 Sedang | Abdul Halim | ✅ Selesai | `.github/` |
| **BK-07** | Dokumentasi panduan kontribusi (`CONTRIBUTING.md`) | 🟡 Sedang | Abdul Halim | ✅ Selesai | `CONTRIBUTING.md` |
| **BK-08** | Penyusunan proposal proyek & data pembagian tugas | 🟡 Sedang | Abdul Halim | ✅ Selesai | `docs/proposal.md`, `tugas-kelompok-1...` |
| **BK-09** | Pembersihan folder kosong di `viewss/` & audit routing `?urls=` | 🟡 Sedang | Alvin / Imam | 📋 Terjadwal (Fase 3) | `frontend/web/viewss/` |
| **BK-10** | Standardisasi penamaan tampilan "Lentera" di web | 🟡 Sedang | Alvin | 📋 Terjadwal (Fase 3) | Seluruh view frontend web |
| **BK-11** | Optimasi responsivitas desktop, tablet, dan smartphone | 🟡 Sedang | Alvin | 📋 Terjadwal (Fase 3) | `frontend/web/csss/styles.css` |
| **BK-12** | Perancangan & implementasi REST API JSON (auth & catalog) | 🔴 Tinggi | Imam | 📋 Terjadwal (Fase 4) | `backend/apps/controllerss/api/` |
| **BK-13** | Inisialisasi basis kode Flutter multiplatform (Android & iOS) | 🔴 Tinggi | Moh. Gilank Alamsyah | 📋 Terjadwal (Fase 5) | `frontend/mobile/` |
| **BK-14** | Pembangunan antarmuka mobile native (login, katalog, detail) | 🔴 Tinggi | Moh. Gilank Alamsyah | 📋 Terjadwal (Fase 5) | `frontend/mobile/lib/` |
| **BK-15** | Pengujian fungsional 15 fitur acuan regresi | 🔴 Tinggi | Seluruh Anggota | 📋 Terjadwal (Fase 6) | `docs/tautan-video-pengujian.md` |
| **BK-16** | Finalisasi berkas laporan resmi `.docx` | 🟡 Sedang | Abdul Halim | 📋 Terjadwal (Fase 6) | `docs/Laporan-Kelompok-01-Lentera.docx` |

---

### Keterangan Status:
- ✅ **Selesai**: Pekerjaan telah tuntas diimplementasikan dan di-commit.
- ⏳ **Menunggu Uji DB**: Menunggu aktivasi modul basis data lokal untuk eksekusi query.
- 📋 **Terjadwal**: Pekerjaan direncanakan pada fase berikutnya sesuai urutan kerja.
