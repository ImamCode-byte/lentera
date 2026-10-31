-- ============================================================
-- 006_works_and_users_unira_seeders.sql
-- Seeder Data Akun Pengguna & Karya Ilmiah Universitas Madura (UNIRA)
-- Password untuk seluruh akun default: password123
-- ============================================================

-- ── 1. Akun Pengguna Sivitas Akademika ──
INSERT INTO users (id, name, username, email, password, role, nim_nip, prodi_id, bio, is_active) VALUES
(1, 'Super Admin LENTERA', 'admin', 'admin@lentera.unira.ac.id', '$2y$10$zQybDVrmwk87gWxpQ7W9E.s91zDTvETPMb/shlyhENP/psEz02U9i', 'admin', '197508102001121001', 9, 'Pengelola Sistem Repositori Karya Ilmiah Universitas Madura', TRUE),
(2, 'Dr. Ahmad Fauzi, M.Kom.', 'kurator', 'kurator@lentera.unira.ac.id', '$2y$10$zQybDVrmwk87gWxpQ7W9E.s91zDTvETPMb/shlyhENP/psEz02U9i', 'curator', '198203152008121003', 9, 'Dosen Informatika & Tim Kurator Naskah Akademik UNIRA', TRUE),
(3, 'Moh. Imam Arifin', 'mahasiswa', 'mahasiswa@lentera.unira.ac.id', '$2y$10$zQybDVrmwk87gWxpQ7W9E.s91zDTvETPMb/shlyhENP/psEz02U9i', 'author', '2021510001', 9, 'Mahasiswa Program Studi Informatika Universitas Madura Angkatan 2021', TRUE),
(4, 'Siti Nurhaliza', 'siti', 'siti@lentera.unira.ac.id', '$2y$10$zQybDVrmwk87gWxpQ7W9E.s91zDTvETPMb/shlyhENP/psEz02U9i', 'author', '2021520015', 2, 'Mahasiswi Program Studi Manajemen Fakultas Ekonomi dan Bisnis UNIRA', TRUE),
(5, 'Budi Santoso', 'budi', 'budi@lentera.unira.ac.id', '$2y$10$zQybDVrmwk87gWxpQ7W9E.s91zDTvETPMb/shlyhENP/psEz02U9i', 'author', '2020530022', 1, 'Mahasiswa Program Studi Hukum Fakultas Hukum Universitas Madura', TRUE)
ON DUPLICATE KEY UPDATE name=VALUES(name), username=VALUES(username), password=VALUES(password), role=VALUES(role), nim_nip=VALUES(nim_nip), prodi_id=VALUES(prodi_id);

-- ── 2. Data Karya Ilmiah Realistis UNIRA ──
INSERT INTO works (id, title, abstract, author_id, prodi_id, work_type_id, status, year, supervisor, view_count, download_count, submitted_at, published_at) VALUES
(
    1,
    'Sistem Informasi Repositori Karya Ilmiah Terpadu Berbasis Web Menggunakan Arsitektur MVC pada Universitas Madura',
    'Penelitian ini mengembangkan repositori karya ilmiah terpadu (LENTERA) untuk mengelola publikasi skripsi, tesis, dan artikel ilmiah di lingkungan Universitas Madura. Sistem dibangun menggunakan arsitektur Model-View-Controller (MVC) dengan bahasa pemrograman PHP dan basis data MySQL. Hasil pengujian menunjukkan efisiensi proses kurasi meningkat sebesar 65% dan akses publik terhadap naskah akademik menjadi lebih cepat, transparan, dan terstruktur.',
    3, 9, 1, 'published', 2024, 'Dr. Ahmad Fauzi, M.Kom.',
    148, 52, '2024-07-15 08:30:00', '2024-08-20 10:00:00'
),
(
    2,
    'Analisis Pengaruh Digital Marketing dan Citra Merek Terhadap Keputusan Pembelian Batik Tulis Madura',
    'Penelitian ini bertujuan untuk menganalisis pengaruh strategi pemasaran digital dan kekuatan merek terhadap keputusan konsumen dalam membeli produk batik tulis khas Madura di Kabupaten Pamekasan. Metode kuantitatif diterapkan melalui kuesioner kepada 120 responden konsumen sentra industri batik Klampar. Hasil analisis regresi linier berganda mengindikasikan bahwa digital marketing dan citra merek berpengaruh signifikan dan simultan terhadap keputusan pembelian.',
    4, 2, 1, 'published', 2024, 'Dr. Hj. Nurul Hidayati, S.E., M.M.',
    95, 31, '2024-08-01 09:15:00', '2024-09-05 14:30:00'
),
(
    3,
    'Implementasi Algoritma C4.5 untuk Klasifikasi Kelayakan Penerima Bantuan Pangan di Kabupaten Pamekasan',
    'Penyaluran bantuan sosial seringkali menghadapi kendala ketepatan sasaran. Penelitian ini menerapkan algoritma data mining C4.5 (decision tree) untuk mengklasifikasikan rumah tangga penerima bantuan pangan non tunai berdasarkan parameter penghasilan, kondisi tempat tinggal, tanggungan keluarga, dan daya listrik. Model pohon keputusan mencapai akurasi sebesar 89.2% pada pengujian validasi 10-fold cross validation.',
    3, 9, 1, 'waiting_payment', 2024, 'Dr. Ahmad Fauzi, M.Kom.',
    42, 0, '2024-10-10 11:20:00', NULL
),
(
    4,
    'Perlindungan Hukum terhadap Hak Cipta Motif Batik Tradisional Pamekasan Berdasarkan Undang-Undang Nomor 28 Tahun 2014',
    'Batik tulis Pamekasan memiliki keanekaragaman motif tradisional yang bernilai tinggi namun rawan terjadi plagiasi oleh industri luar daerah. Skripsi ini mengkaji bentuk kepastian hukum dan efektivitas penerapan Undang-Undang Nomor 28 Tahun 2014 tentang Hak Cipta bagi para pengrajin batik skala mikro di Madura serta peran pemerintah daerah dalam fasilitasi pendaftaran indikasi geografis dan perlindungan kekayaan intelektual komunal.',
    5, 1, 1, 'in_review', 2024, 'Prof. Dr. H. Faisal Rahman, S.H., M.Hum.',
    23, 0, '2024-10-18 13:45:00', NULL
),
(
    5,
    'Rancang Bangun Aplikasi Monitoring Hasil Panen Tembakau Madura Berbasis Mobile Flutter',
    'Pertanian tembakau merupakan komoditas perkebunan unggulan di Pulau Madura. Aplikasi mobile berbasis Flutter dirancang guna memfasilitasi petani dalam mencatat estimasi tonase, kalender tanam, biaya operasional, dan pemantauan harga pasar gudang secara waktu nyata. Sistem terintegrasi dengan REST API backend PHP untuk sinkronisasi data lintas perangkat smartphone Android dan iOS.',
    3, 9, 1, 'revision', 2024, 'Dr. Ahmad Fauzi, M.Kom.',
    35, 0, '2024-10-05 10:00:00', NULL
)
ON DUPLICATE KEY UPDATE title=VALUES(title), abstract=VALUES(abstract), status=VALUES(status);

-- ── 3. Kata Kunci Karya Ilmiah ──
INSERT INTO work_keywords (work_id, keyword) VALUES
(1, 'Repositori Digital'),
(1, 'Karya Ilmiah'),
(1, 'Model View Controller'),
(1, 'Universitas Madura'),
(1, 'Informatika'),
(2, 'Digital Marketing'),
(2, 'Citra Merek'),
(2, 'Batik Tulis Madura'),
(2, 'Pamekasan'),
(2, 'Keputusan Pembelian'),
(3, 'Algoritma C4.5'),
(3, 'Data Mining'),
(3, 'Klasifikasi'),
(3, 'Bantuan Sosial Pangan'),
(3, 'Pamekasan'),
(4, 'Hak Cipta'),
(4, 'Perlindungan Hukum'),
(4, 'Batik Tradisional'),
(4, 'Kekayaan Intelektual'),
(4, 'UU Hak Cipta'),
(5, 'Flutter Mobile'),
(5, 'Monitoring Pertanian'),
(5, 'Tembakau Madura'),
(5, 'REST API'),
(5, 'Android dan iOS');

-- ── 4. Berkas Naskah PDF ──
INSERT INTO work_files (id, work_id, original_name, stored_name, file_path, file_size, mime_type, version, is_current) VALUES
(1, 1, 'Skripsi_Informatika_Moh_Imam_Arifin_2024.pdf', '20b59e727f7dbff0cad2f377f281b6e0.pdf', 'uploads/works/20b59e727f7dbff0cad2f377f281b6e0.pdf', 18750, 'application/pdf', 1, TRUE),
(2, 2, 'Skripsi_Manajemen_Siti_Nurhaliza_2024.pdf', '20b59e727f7dbff0cad2f377f281b6e0.pdf', 'uploads/works/20b59e727f7dbff0cad2f377f281b6e0.pdf', 18750, 'application/pdf', 1, TRUE),
(3, 3, 'Draf_Skripsi_C45_Bantuan_Pangan_2024.pdf', '20b59e727f7dbff0cad2f377f281b6e0.pdf', 'uploads/works/20b59e727f7dbff0cad2f377f281b6e0.pdf', 18750, 'application/pdf', 1, TRUE),
(4, 4, 'Draf_Skripsi_Hukum_Batik_Budi_2024.pdf', '20b59e727f7dbff0cad2f377f281b6e0.pdf', 'uploads/works/20b59e727f7dbff0cad2f377f281b6e0.pdf', 'uploads/works/20b59e727f7dbff0cad2f377f281b6e0.pdf', 18750, 'application/pdf', 1, TRUE),
(5, 5, 'Draf_Skripsi_Flutter_Tembakau_v1.pdf', '20b59e727f7dbff0cad2f377f281b6e0.pdf', 'uploads/works/20b59e727f7dbff0cad2f377f281b6e0.pdf', 18750, 'application/pdf', 1, TRUE)
ON DUPLICATE KEY UPDATE original_name=VALUES(original_name);

-- ── 5. Catatan Telaah Kurator ──
INSERT INTO reviews (id, work_id, curator_id, action, comment, created_at) VALUES
(1, 1, 2, 'approve', 'Naskah telah memenuhi kaidah penulisan akademik dan implementasi sistem telah terverifikasi dengan sangat baik. Naskah disetujui untuk diterbitkan di etalase repositori publik LENTERA.', '2024-08-20 09:45:00'),
(2, 2, 2, 'approve', 'Analisis data statistik regresi linier berganda telah valid dan pembahasan selaras dengan temuan di lapangan sentra batik Klampar. Disetujui terbit.', '2024-09-05 14:15:00'),
(3, 3, 2, 'acc_payment', 'Naskah dinyatakan lolos kurasi substansi akademik. Silakan selesaikan administrasi publikasi (Article Processing Charge) melalui QRIS institusi sebelum naskah diterbitkan.', '2024-10-12 10:00:00'),
(4, 4, 2, 'in_review', 'Naskah sedang dalam antrean telaah orisinalitas dan kesesuaian format perundang-undangan.', '2024-10-18 14:00:00'),
(5, 5, 2, 'revision', 'Perlu dilakukan perbaikan diagram relasi entitas pada Bab III serta penambahan pengujian fungsional pada modul geolokasi perkebunan tembakau sebelum disetujui.', '2024-10-08 15:30:00')
ON DUPLICATE KEY UPDATE comment=VALUES(comment);

-- ── 6. Transaksi Pembayaran Publikasi (Article Processing Charge) ──
INSERT INTO paymentss (id, work_id, user_id, invoice_number, amount, payment_method, destination_account, status, created_at) VALUES
(1, 3, 3, 'INV-LTR-20241012-0003', 150000.00, 'QRIS Dhes Mart / DANA', '087849264452 an. Imam (Dhes Mart)', 'pending', '2024-10-12 10:05:00')
ON DUPLICATE KEY UPDATE amount=VALUES(amount);

