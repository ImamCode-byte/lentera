# Panduan Kontribusi — Kelompok 01 Lentera

Dokumen ini memuat standar dan alur kerja teknis yang **wajib dipatuhi** oleh seluruh anggota Kelompok 01 dalam pengerjaan proyek mata kuliah **Pemrograman Berbasis Platform**.

---

## 1. Alur Kerja Git & Branching

1. **Branch Utama (`main`) Dilindungi**:
   - Dilarang keras melakukan commit atau perubahan langsung pada branch `main`.
   - Branch `main` hanya menerima perubahan melalui penggabungan *Pull Request* yang telah disetujui.

2. **Penggunaan Branch Tugas**:
   - Setiap anggota membuat branch kerja terpisah dari branch `main` terbaru sebelum mengerjakan fitur/tugas:
     ```bash
     git checkout main
     git pull --ff-only
     git checkout -b <nama-branch-tugas>
     ```
   - Contoh penamaan branch:
     - `fase-1-keamanan-imam`
     - `web-responsif-alvin`
     - `mobile-flutter-gilank`
     - `dokumen-halim`

---

## 2. Format Pesan Commit (Wajib)

Setiap perubahan wajib di-commit dengan pesan terstruktur mengikuti format:
```text
<tipe>(<bagian>): <ringkasan perubahan>
```

### Jenis Tipe Commit:
- **`feat`**: Penambahan fitur, fungsi, atau tugas baru.
- **`fix`**: Perbaikan bug, celah keamanan, atau galat fungsional.
- **`docs`**: Pembaruan dokumentasi, laporan, atau berkas pendukung.
- **`test`**: Penambahan atau pembaruan skenario pengujian dan bukti uji.

### Contoh Pesan Commit:
- `fix(backend): lindungi folder uploads dari akses langsung via .htaccess`
- `feat(frontend-web): buat tampilan kartu karya ilmiah yang responsif`
- `feat(frontend-mobile): buat halaman login dengan validasi formulir`
- `docs(kelompok-01): perbarui proposal proyek dan matriks backlog`
- `test(frontend-web): tambah bukti pengujian unduhan naskah terbit`

> [!IMPORTANT]
> - Satu commit berisi **satu perubahan terfokus**.
> - **Dilarang keras** memasukkan kata sandi, token API, file `.env` asli, atau folder dependensi (`node_modules/`, `vendor/`) ke dalam commit.

---

## 3. Alur Pull Request & Review Silang

1. Setiap fitur atau perbaikan yang selesai dikerjakan pada branch tugas didorong ke GitHub:
   ```bash
   git push -u origin <nama-branch-tugas>
   ```
2. Buka **Pull Request (PR)** baru dengan target branch `main`.
3. Gunakan template PR default yang tersedia dan lengkapi:
   - Ringkasan tugas dan berkas yang diubah.
   - Langkah pengujian beserta bukti tangkapan layar.
   - Tautan issue terkait jika ada.
4. **Mekanisme Review Silang Melingkar**:
   - Setiap PR wajib mendapatkan review substantif dan persetujuan dari rekan kelompok sebelum di-merge:
     - PR **Imam** ditinjau oleh **Alvin**.
     - PR **Alvin** ditinjau oleh **Gilank**.
     - PR **Gilank** ditinjau oleh **Abdul Halim**.
     - PR **Abdul Halim** ditinjau oleh **Imam**.
5. Setelah disetujui dan di-merge, seluruh anggota memperbarui branch `main` lokal masing-masing:
   ```bash
   git checkout main
   git pull --ff-only
   ```

---

## 4. Pencatatan Kendala (GitHub Issues)

Setiap kendala teknis atau hambatan yang tidak dapat diselesaikan seketika **wajib dicatat sebagai GitHub Issue**:
1. Buat Issue baru dengan judul berawalan `[Kendala] ...`.
2. Isi formulir kendala menggunakan template resmi:
   - Pertemuan atau tugas terkait.
   - Deskripsi masalah dan perilaku yang diharapkan.
   - Langkah-langkah reproduksi galat.
   - Bukti tangkapan layar/log dan upaya perbaikan yang telah dicoba.
3. Setelah kendala terselesaikan melalui perbaikan kode, tautkan nomor commit atau PR penyelesaian sebelum menutup Issue tersebut.
