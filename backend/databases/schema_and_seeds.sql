-- ============================================================
-- 🔥 LENTERA — Master Schema & Seed SQL (db_lentera)
-- Laman Etalase Karya Tulis Ilmiah Terpadu Akademik
-- Universitas Madura (UNIRA)
-- 
-- Petunjuk:
-- 1. Buat database: CREATE DATABASE IF NOT EXISTS db_lentera CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 2. Import file ini langsung ke db_lentera via phpMyAdmin atau CLI:
--    mysql -u root -p db_lentera < schema_and_seeds.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_lentera
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE db_lentera;

SET FOREIGN_KEY_CHECKS = 0;

-- ────────────────────────────────────────────────────────────
-- 1. TABEL FACULTIES
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS faculties (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    code        VARCHAR(10)  NOT NULL,
    name        VARCHAR(100) NOT NULL,
    sort_order  INT DEFAULT 0,
    is_active   BOOLEAN DEFAULT TRUE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_faculties_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 2. TABEL STUDY_PROGRAMS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS study_programs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    code        VARCHAR(20)  NOT NULL,
    name        VARCHAR(100) NOT NULL,
    degree      ENUM('S1','S2') NOT NULL,
    faculty_id  INT NOT NULL,
    is_active   BOOLEAN DEFAULT TRUE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_programs_code (code),
    INDEX idx_programs_faculty (faculty_id),

    CONSTRAINT fk_programs_faculty
        FOREIGN KEY (faculty_id) REFERENCES faculties(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 3. TABEL USERS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id                    INT AUTO_INCREMENT PRIMARY KEY,
    name                  VARCHAR(100) NOT NULL,
    username              VARCHAR(60) NULL,
    email                 VARCHAR(150) NOT NULL,
    password              VARCHAR(255) NOT NULL,
    role                  ENUM('author','curator','admin') NOT NULL DEFAULT 'author',
    nim_nip               VARCHAR(30) NULL,
    prodi_id              INT NULL,
    affiliation           VARCHAR(150) NULL DEFAULT 'Universitas Madura',
    country               VARCHAR(60) NULL DEFAULT 'Indonesia',
    phone                 VARCHAR(30) NULL,
    orcid_id              VARCHAR(50) NULL,
    avatar                VARCHAR(255) NULL,
    bio                   TEXT NULL,
    is_reviewer_candidate BOOLEAN DEFAULT FALSE,
    is_active             BOOLEAN DEFAULT TRUE,
    last_login_at         TIMESTAMP NULL,
    created_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_users_email (email),
    UNIQUE KEY uk_users_username (username),
    INDEX idx_users_role (role),
    INDEX idx_users_prodi (prodi_id),

    CONSTRAINT fk_users_prodi
        FOREIGN KEY (prodi_id) REFERENCES study_programs(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 4. TABEL WORK_TYPES
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS work_types (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    code        VARCHAR(10) NOT NULL,
    name        VARCHAR(50) NOT NULL,
    description TEXT NULL,
    is_active   BOOLEAN DEFAULT TRUE,

    UNIQUE KEY uk_types_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 5. TABEL WORKS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS works (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    title          VARCHAR(500) NOT NULL,
    abstract       TEXT NOT NULL,
    author_id      INT NOT NULL,
    prodi_id       INT NOT NULL,
    work_type_id   INT NOT NULL,
    status         ENUM('draft','submitted','in_review','revision','waiting_payment','published','rejected')
                       NOT NULL DEFAULT 'draft',
    year           SMALLINT NOT NULL,
    supervisor     VARCHAR(200) NULL,
    view_count     INT UNSIGNED DEFAULT 0,
    download_count INT UNSIGNED DEFAULT 0,
    submitted_at   TIMESTAMP NULL,
    published_at   TIMESTAMP NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_works_status (status),
    INDEX idx_works_author (author_id),
    INDEX idx_works_prodi (prodi_id),
    INDEX idx_works_type (work_type_id),
    INDEX idx_works_year (year),
    INDEX idx_works_published (published_at),
    FULLTEXT INDEX ft_works_search (title, abstract),

    CONSTRAINT fk_works_author
        FOREIGN KEY (author_id) REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_works_prodi
        FOREIGN KEY (prodi_id) REFERENCES study_programs(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_works_type
        FOREIGN KEY (work_type_id) REFERENCES work_types(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 6. TABEL WORK_KEYWORDS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS work_keywords (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    work_id INT NOT NULL,
    keyword VARCHAR(50) NOT NULL,

    INDEX idx_keywords_work (work_id),
    INDEX idx_keywords_keyword (keyword),

    CONSTRAINT fk_keywords_work
        FOREIGN KEY (work_id) REFERENCES works(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 7. TABEL WORK_FILES
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS work_files (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    work_id       INT NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name   VARCHAR(255) NOT NULL,
    file_path     VARCHAR(500) NOT NULL,
    file_size     INT UNSIGNED NOT NULL,
    mime_type     VARCHAR(100) DEFAULT 'application/pdf',
    version       INT DEFAULT 1,
    is_current    BOOLEAN DEFAULT TRUE,
    uploaded_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_files_work (work_id),

    CONSTRAINT fk_files_work
        FOREIGN KEY (work_id) REFERENCES works(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 8. TABEL REVIEWS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reviews (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    work_id    INT NOT NULL,
    curator_id INT NOT NULL,
    action     ENUM('approve','revision','reject','in_review','acc_payment') NOT NULL,
    comment    TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_reviews_work (work_id),
    INDEX idx_reviews_curator (curator_id),

    CONSTRAINT fk_reviews_work
        FOREIGN KEY (work_id) REFERENCES works(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_reviews_curator
        FOREIGN KEY (curator_id) REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 9. TABEL PAYMENTSS (Biaya Publikasi / APC)
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS paymentss (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    work_id             INT NOT NULL,
    user_id             INT NOT NULL,
    invoice_number      VARCHAR(50) NOT NULL,
    amount              DECIMAL(12,2) NOT NULL DEFAULT 150000.00,
    payment_method      VARCHAR(50) DEFAULT 'QRIS Dhes Mart / DANA',
    destination_account VARCHAR(100) DEFAULT '087849264452 an. Imam (Dhes Mart)',
    sender_name         VARCHAR(100) NULL,
    reference_number    VARCHAR(100) NULL,
    proof_file          VARCHAR(255) NULL,
    status              ENUM('pending','completed','rejected') NOT NULL DEFAULT 'pending',
    paid_at             TIMESTAMP NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_paymentss_invoice (invoice_number),
    INDEX idx_paymentss_work (work_id),
    INDEX idx_paymentss_user (user_id),

    CONSTRAINT fk_paymentss_work
        FOREIGN KEY (work_id) REFERENCES works(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_paymentss_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 10. TABEL DOWNLOAD_LOGS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS download_logs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    work_id       INT NULL,
    user_id       INT NULL,
    ip_address    VARCHAR(45) NOT NULL,
    user_agent    VARCHAR(500) NULL,
    downloaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_downloads_work (work_id),
    INDEX idx_downloads_date (downloaded_at),

    CONSTRAINT fk_downloads_work
        FOREIGN KEY (work_id) REFERENCES works(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_downloads_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 11. TABEL NOTIFICATIONS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS notifications (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    title      VARCHAR(100) NOT NULL,
    message    TEXT NOT NULL,
    link       VARCHAR(255) NULL,
    is_read    BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_notif_user (user_id, is_read),
    INDEX idx_notif_date (created_at),

    CONSTRAINT fk_notif_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 12. TABEL PASSWORD_RESETS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS password_resets (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    token      VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    is_used    BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uk_resets_token (token),
    INDEX idx_resets_user (user_id),

    CONSTRAINT fk_resets_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 13. TABEL ACTIVITY_LOGS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS activity_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NULL,
    action      VARCHAR(50) NOT NULL,
    target_type VARCHAR(100) NULL,
    target_id   INT NULL,
    description TEXT NULL,
    ip_address  VARCHAR(45) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_logs_user (user_id),
    INDEX idx_logs_action (action),
    INDEX idx_logs_date (created_at),

    CONSTRAINT fk_logs_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- 14. TABEL SETTINGS
-- ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS settings (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    setting_key   VARCHAR(50) NOT NULL,
    setting_value TEXT NULL,
    setting_group VARCHAR(50) DEFAULT 'general',
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_settings_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 📦 SEEDER DATA AWAL UNIVERSITAS MADURA (UNIRA)
-- ============================================================

-- ── 1. Fakultas UNIRA ──
INSERT INTO faculties (id, code, name, sort_order) VALUES
(1, 'FH',   'Fakultas Hukum',                                1),
(2, 'FEB',  'Fakultas Ekonomi dan Bisnis',                    2),
(3, 'FIA',  'Fakultas Ilmu Administrasi',                     3),
(4, 'FP',   'Fakultas Pertanian',                             4),
(5, 'FT',   'Fakultas Teknik',                                5),
(6, 'FKIP', 'Fakultas Keguruan dan Ilmu Pendidikan',          6),
(7, 'PPS',  'Program Pascasarjana',                           7)
ON DUPLICATE KEY UPDATE name=VALUES(name), sort_order=VALUES(sort_order);

-- ── 2. Program Studi UNIRA ──
INSERT INTO study_programs (id, code, name, degree, faculty_id) VALUES
(1,  'FH-HKM',   'Hukum',                       'S1', 1),
(2,  'FEB-MNJ',  'Manajemen',                   'S1', 2),
(3,  'FEB-AKT',  'Akuntansi',                   'S1', 2),
(4,  'FEB-BDG',  'Bisnis Digital',              'S1', 2),
(5,  'FIA-ADP',  'Administrasi Publik',         'S1', 3),
(6,  'FP-PTK',   'Peternakan',                  'S1', 4),
(7,  'FP-AGB',   'Agribisnis',                  'S1', 4),
(8,  'FT-TSP',   'Teknik Sipil',               'S1', 5),
(9,  'FT-INF',   'Informatika',                'S1', 5),
(10, 'FT-TIN',   'Teknik Industri',            'S1', 5),
(11, 'FKIP-PBI', 'Pendidikan Bahasa Indonesia', 'S1', 6),
(12, 'FKIP-PMT', 'Pendidikan Matematika',      'S1', 6),
(13, 'FKIP-PBG', 'Pendidikan Bahasa Inggris',  'S1', 6),
(14, 'PPS-HKM',  'Magister Hukum',             'S2', 7),
(15, 'PPS-MNJ',  'Magister Manajemen',         'S2', 7)
ON DUPLICATE KEY UPDATE name=VALUES(name), degree=VALUES(degree), faculty_id=VALUES(faculty_id);

-- ── 3. Tipe Karya Ilmiah ──
INSERT INTO work_types (id, code, name, description) VALUES
(1, 'SKR', 'Skripsi',               'Tugas akhir jenjang sarjana (S1)'),
(2, 'TES', 'Tesis',                 'Tugas akhir jenjang magister (S2)'),
(3, 'JRN', 'Jurnal Ilmiah',         'Artikel ilmiah yang dipublikasikan di jurnal atau prosiding'),
(4, 'LPK', 'Laporan Kerja Praktik', 'Laporan kegiatan kerja praktik atau magang industri'),
(5, 'LPN', 'Laporan Penelitian',    'Laporan hasil penelitian mandiri atau hibah dosen dan mahasiswa')
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description);

-- ── 4. Akun Pengguna Sivitas Akademika ──
-- Catatan: Password untuk semua akun default di bawah adalah: password123
INSERT INTO users (id, name, username, email, password, role, nim_nip, prodi_id, bio, is_active) VALUES
(1, 'Super Admin LENTERA', 'admin', 'admin@lentera.unira.ac.id', '$2y$10$zQybDVrmwk87gWxpQ7W9E.s91zDTvETPMb/shlyhENP/psEz02U9i', 'admin', '197508102001121001', 9, 'Pengelola Sistem Repositori Karya Ilmiah Universitas Madura', TRUE),
(2, 'Dr. Ahmad Fauzi, M.Kom.', 'kurator', 'kurator@lentera.unira.ac.id', '$2y$10$zQybDVrmwk87gWxpQ7W9E.s91zDTvETPMb/shlyhENP/psEz02U9i', 'curator', '198203152008121003', 9, 'Dosen Informatika & Tim Kurator Naskah Akademik UNIRA', TRUE),
(3, 'Moh. Imam Arifin', 'mahasiswa', 'mahasiswa@lentera.unira.ac.id', '$2y$10$zQybDVrmwk87gWxpQ7W9E.s91zDTvETPMb/shlyhENP/psEz02U9i', 'author', '2021510001', 9, 'Mahasiswa Program Studi Informatika Universitas Madura Angkatan 2021', TRUE),
(4, 'Siti Nurhaliza', 'siti', 'siti@lentera.unira.ac.id', '$2y$10$zQybDVrmwk87gWxpQ7W9E.s91zDTvETPMb/shlyhENP/psEz02U9i', 'author', '2021520015', 2, 'Mahasiswi Program Studi Manajemen Fakultas Ekonomi dan Bisnis UNIRA', TRUE),
(5, 'Budi Santoso', 'budi', 'budi@lentera.unira.ac.id', '$2y$10$zQybDVrmwk87gWxpQ7W9E.s91zDTvETPMb/shlyhENP/psEz02U9i', 'author', '2020530022', 1, 'Mahasiswa Program Studi Hukum Fakultas Hukum Universitas Madura', TRUE)
ON DUPLICATE KEY UPDATE name=VALUES(name), username=VALUES(username), password=VALUES(password), role=VALUES(role), nim_nip=VALUES(nim_nip), prodi_id=VALUES(prodi_id);

-- ── 5. Data Karya Ilmiah Realistis UNIRA ──
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

-- ── 6. Kata Kunci Karya Ilmiah ──
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

-- ── 7. Berkas Naskah PDF ──
INSERT INTO work_files (id, work_id, original_name, stored_name, file_path, file_size, mime_type, version, is_current) VALUES
(1, 1, 'Skripsi_Informatika_Moh_Imam_Arifin_2024.pdf', '20b59e727f7dbff0cad2f377f281b6e0.pdf', 'uploads/works/20b59e727f7dbff0cad2f377f281b6e0.pdf', 18750, 'application/pdf', 1, TRUE),
(2, 2, 'Skripsi_Manajemen_Siti_Nurhaliza_2024.pdf', '20b59e727f7dbff0cad2f377f281b6e0.pdf', 'uploads/works/20b59e727f7dbff0cad2f377f281b6e0.pdf', 18750, 'application/pdf', 1, TRUE),
(3, 3, 'Draf_Skripsi_C45_Bantuan_Pangan_2024.pdf', '20b59e727f7dbff0cad2f377f281b6e0.pdf', 'uploads/works/20b59e727f7dbff0cad2f377f281b6e0.pdf', 18750, 'application/pdf', 1, TRUE),
(4, 4, 'Draf_Skripsi_Hukum_Batik_Budi_2024.pdf', '20b59e727f7dbff0cad2f377f281b6e0.pdf', 'uploads/works/20b59e727f7dbff0cad2f377f281b6e0.pdf', 18750, 'application/pdf', 1, TRUE),
(5, 5, 'Draf_Skripsi_Flutter_Tembakau_v1.pdf', '20b59e727f7dbff0cad2f377f281b6e0.pdf', 'uploads/works/20b59e727f7dbff0cad2f377f281b6e0.pdf', 18750, 'application/pdf', 1, TRUE)
ON DUPLICATE KEY UPDATE original_name=VALUES(original_name);

-- ── 8. Catatan Telaah Kurator ──
INSERT INTO reviews (id, work_id, curator_id, action, comment, created_at) VALUES
(1, 1, 2, 'approve', 'Naskah telah memenuhi kaidah penulisan akademik dan implementasi sistem telah terverifikasi dengan sangat baik. Naskah disetujui untuk diterbitkan di etalase repositori publik LENTERA.', '2024-08-20 09:45:00'),
(2, 2, 2, 'approve', 'Analisis data statistik regresi linier berganda telah valid dan pembahasan selaras dengan temuan di lapangan sentra batik Klampar. Disetujui terbit.', '2024-09-05 14:15:00'),
(3, 3, 2, 'acc_payment', 'Naskah dinyatakan lolos kurasi substansi akademik. Silakan selesaikan administrasi publikasi (Article Processing Charge) melalui QRIS institusi sebelum naskah diterbitkan.', '2024-10-12 10:00:00'),
(4, 4, 2, 'in_review', 'Naskah sedang dalam antrean telaah orisinalitas dan kesesuaian format perundang-undangan.', '2024-10-18 14:00:00'),
(5, 5, 2, 'revision', 'Perlu dilakukan perbaikan diagram relasi entitas pada Bab III serta penambahan pengujian fungsional pada modul geolokasi perkebunan tembakau sebelum disetujui.', '2024-10-08 15:30:00')
ON DUPLICATE KEY UPDATE comment=VALUES(comment);

-- ── 9. Transaksi Pembayaran Publikasi (Article Processing Charge) ──
INSERT INTO paymentss (id, work_id, user_id, invoice_number, amount, payment_method, destination_account, status, created_at) VALUES
(1, 3, 3, 'INV-LTR-20241012-0003', 150000.00, 'QRIS Dhes Mart / DANA', '087849264452 an. Imam (Dhes Mart)', 'pending', '2024-10-12 10:05:00')
ON DUPLICATE KEY UPDATE amount=VALUES(amount);

-- ── 10. Pengaturan Sistem Awal ──
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('site_name',          'LENTERA',                                 'general'),
('site_tagline',       'Menerangi Karya, Menginspirasi Generasi', 'general'),
('university_name',    'Universitas Madura',                      'general'),
('university_url',     'https://unira.ac.id',                     'general'),
('contact_email',      'lentera@unira.ac.id',                     'contact'),
('contact_phone',      '+62 812-3456-7890',                       'contact'),
('contact_address',    'Jl. Raya Panglegur No. 3, Pamekasan, Madura, Jawa Timur', 'contact'),
('max_file_size',      '20971520',                                'upload'),
('allowed_formats',    'pdf',                                     'upload'),
('submission_rules',   'Format PDF maksimal 20MB. Pastikan naskah telah disetujui dosen pembimbing.', 'submission')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value), setting_group=VALUES(setting_group);
