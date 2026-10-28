# Frontend Mobile — Aplikasi Native Lentera (Flutter)

Direktori ini memuat basis kode aplikasi mobile native **Lentera** untuk platform Android dan iOS yang dikembangkan menggunakan **Flutter Framework**.

---

## 1. Tujuan
- Menyediakan akses cepat dan nyaman bagi mahasiswa, dosen, dan peneliti untuk mencari, membaca abstrak, dan mengunduh karya ilmiah langsung dari ponsel pintar.
- Menghadirkan pengalaman pengguna native (*smooth navigation*, *offline handling*, *safe area compliance* untuk notch dan bilah navigasi ponsel).
- Mengintegrasikan antarmuka mobile dengan backend REST API terpusat.

---

## 2. Fitur Aplikasi Mobile

### Fitur yang Tersedia (Fase 5 Target):
- ✅ **Halaman Beranda & Eksplorasi**: Menampilkan koleksi naskah terbaru dan sorotan repositori akademik.
- ✅ **Pencarian Cepat & Filter**: Penelusuran katalog karya ilmiah secara realtime berdasarkan kata kunci judul dan penulis.
- ✅ **Tampilan Detail Karya Ilmiah**: Membaca abstrak lengkap, informasi penulis, pembimbing, dan metadata naskah.
- ✅ **Autentikasi Pengguna**: Formulir login aman untuk penulis dan civitas akademika dengan penanganan token API.
- ✅ **Ketangguhan Jaringan**: Penanganan kondisi tanpa internet (*offline detection*), timeout, *loading state*, dan *empty state*.

### Fitur yang Direncanakan (Tahap Lanjutan):
- 📋 Formulir pengunggahan naskah dan dokumen PDF langsung dari penyimpanan ponsel.
- 📋 Notifikasi push saat status telaah karya diperbarui oleh kurator.

---

## 3. Prasyarat & Versi Tooling SDK
- **Flutter SDK**: Versi stable 3.19.0+ atau lebih baru
- **Dart SDK**: Versi 3.3.0+
- **Android Studio / VS Code** dilengkapi ekstensi *Flutter & Dart*
- **Android SDK**: API Level 34 (Android 14) atau minimum API Level 21 (Android 5.0 Lollipop)
- **Java Development Kit (JDK)**: OpenJDK 17

---

## 4. Cara Menjalankan di Emulator Android

1. **Masuk ke Direktori Mobile**:
   ```bash
   cd frontend/mobile
   ```

2. **Unduh Seluruh Dependensi**:
   ```bash
   flutter pub get
   ```

3. **Konfigurasi Alamat API**:
   - Salin berkas konfigurasi lingkungan:
     ```bash
     copy .env.example .env
     ```
   - Sesuaikan alamat host API (gunakan `http://10.0.2.2/lentera/backend/api` jika menggunakan emulator Android bawaan, atau alamat IP LAN komputer server jika menggunakan perangkat fisik).

4. **Jalankan Aplikasi pada Emulator**:
   - Buka Android Device Manager dan jalankan emulator Android.
   - Eksekusi perintah:
     ```bash
     flutter run
     ```

---

## 5. Cara Melakukan Build Berkas APK Android

Untuk menghasilkan berkas instalasi APK release mandiri:
```bash
flutter build apk --release
```
Berkas APK yang dihasilkan akan berada pada direktori:
`build/app/outputs/flutter-apk/app-release.apk`

---

## 6. Catatan Pengujian & Status Tooling Lingkungan

> [!IMPORTANT]
> **Status Audit Lingkungan Lokal**:  
> Seluruh basis kode Flutter (`pubspec.yaml`, model data, `ApiService` dengan penanganan offline/timeout, widget state adaptif, serta layar antarmuka `home`, `search`, `detail`, `login`, dan `author_works`) telah selesai dibangun dan terstruktur rapi. Namun karena **Flutter SDK belum terpasang pada PATH sistem operasi komputer ini**, perintah analisis statis (`flutter analyze`) dan kompilasi emulator belum dijalankan secara langsung di workstation ini (sesuai aturan dosen: tidak mengarang hasil pengujian).

> [!NOTE]
> **Pengujian Platform iOS (Apple)**:  
> Kompilasi dan pengujian untuk platform iOS memerlukan komputer berbasis **macOS** dengan **Xcode**. Karena workstation ini menggunakan sistem operasi Windows, aplikasi mobile native secara jujur **belum diuji di lingkungan iOS nyata**. Basis kode Flutter dirancang sepenuhnya multiplatform dan siap dikompilasi pada perangkat macOS.
