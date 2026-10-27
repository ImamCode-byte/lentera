-- ============================================================
-- 🔥 LENTERA — Master Schema & Seed SQL
-- Laman Etalase Karya Tulis Ilmiah Terpadu Akademik
-- Universitas Madura (UNIRA)
-- 
-- Petunjuk:
-- 1. Buat database: CREATE DATABASE IF NOT EXISTS lentera_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 2. Import file ini langsung ke lentera_db via phpMyAdmin atau CLI.
-- ============================================================

CREATE DATABASE IF NOT EXISTS lentera_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE lentera_db;

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
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL,
    password      VARCHAR(255) NOT NULL,
    role          ENUM('author','curator','admin') NOT NULL DEFAULT 'author',
    nim_nip       VARCHAR(30) NULL,
    prodi_id      INT NULL,
    avatar        VARCHAR(255) NULL,
    bio           TEXT NULL,
    is_active     BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_users_email (email),
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
    status         ENUM('draft','submitted','in_review','revision','published','rejected')
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
    id       INT AUTO_INCREMENT PRIMARY KEY,
    work_id  INT NOT NULL,
    keyword  VARCHAR(100) NOT NULL,

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
    file_size     BIGINT UNSIGNED NOT NULL,
    version       INT NOT NULL DEFAULT 1,
    is_current    BOOLEAN DEFAULT TRUE,
    uploaded_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_files_work (work_id),
    INDEX idx_files_current (work_id, is_current),

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
    action     ENUM('approve','revision','reject') NOT NULL,
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
-- 9. TABEL DOWNLOAD_LOGS
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
-- 10. TABEL NOTIFICATIONS
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
-- 11. TABEL PASSWORD_RESETS
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
-- 12. TABEL ACTIVITY_LOGS
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
-- 13. TABEL SETTINGS
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
-- 📦 SEEDER DATA AWAL
-- ============================================================

-- ── 1. Fakultas UNIRA ──
INSERT INTO faculties (code, name, sort_order) VALUES
('FH',   'Fakultas Hukum',                                1),
('FEB',  'Fakultas Ekonomi dan Bisnis',                    2),
('FIA',  'Fakultas Ilmu Administrasi',                     3),
('FP',   'Fakultas Pertanian',                             4),
('FT',   'Fakultas Teknik',                                5),
('FKIP', 'Fakultas Keguruan dan Ilmu Pendidikan',          6),
('PPS',  'Program Pascasarjana',                           7)
ON DUPLICATE KEY UPDATE name=VALUES(name), sort_order=VALUES(sort_order);

-- ── 2. Program Studi UNIRA ──
INSERT INTO study_programs (code, name, degree, faculty_id) VALUES
('FH-HKM',   'Hukum',                       'S1', (SELECT id FROM faculties WHERE code='FH')),
('FEB-MNJ',  'Manajemen',                   'S1', (SELECT id FROM faculties WHERE code='FEB')),
('FEB-AKT',  'Akuntansi',                   'S1', (SELECT id FROM faculties WHERE code='FEB')),
('FEB-BDG',  'Bisnis Digital',              'S1', (SELECT id FROM faculties WHERE code='FEB')),
('FIA-ADP',  'Administrasi Publik',         'S1', (SELECT id FROM faculties WHERE code='FIA')),
('FP-PTK',   'Peternakan',                  'S1', (SELECT id FROM faculties WHERE code='FP')),
('FP-AGB',   'Agribisnis',                  'S1', (SELECT id FROM faculties WHERE code='FP')),
('FT-TSP',   'Teknik Sipil',               'S1', (SELECT id FROM faculties WHERE code='FT')),
('FT-INF',   'Informatika',                'S1', (SELECT id FROM faculties WHERE code='FT')),
('FT-TIN',   'Teknik Industri',            'S1', (SELECT id FROM faculties WHERE code='FT')),
('FKIP-PBI', 'Pendidikan Bahasa Indonesia', 'S1', (SELECT id FROM faculties WHERE code='FKIP')),
('FKIP-PMT', 'Pendidikan Matematika',      'S1', (SELECT id FROM faculties WHERE code='FKIP')),
('FKIP-PBG', 'Pendidikan Bahasa Inggris',  'S1', (SELECT id FROM faculties WHERE code='FKIP')),
('PPS-HKM',  'Hukum',                      'S2', (SELECT id FROM faculties WHERE code='PPS')),
('PPS-MNJ',  'Manajemen',                  'S2', (SELECT id FROM faculties WHERE code='PPS'))
ON DUPLICATE KEY UPDATE name=VALUES(name), degree=VALUES(degree), faculty_id=VALUES(faculty_id);

-- ── 3. Tipe Karya Ilmiah ──
INSERT INTO work_types (code, name, description) VALUES
('SKR', 'Skripsi',               'Tugas akhir jenjang sarjana (S1)'),
('TES', 'Tesis',                 'Tugas akhir jenjang magister (S2)'),
('JRN', 'Jurnal Ilmiah',         'Artikel ilmiah yang dipublikasikan di jurnal atau prosiding'),
('LPK', 'Laporan Kerja Praktik', 'Laporan kegiatan kerja praktik atau magang industri'),
('LPN', 'Laporan Penelitian',    'Laporan hasil penelitian mandiri atau hibah dosen dan mahasiswa')
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description);

-- ── 4. Akun Super Admin Default ──
INSERT INTO users (name, email, password, role, is_active) VALUES
(
    'Super Admin LENTERA',
    'admin@lentera.unira.ac.id',
    '$2y$12$4L64g0d2j5HkU7bK0P2XqO6uWvR8bF9wB3nQ4mE7yZ1cT5aV0gK2e',
    'admin',
    TRUE
)
ON DUPLICATE KEY UPDATE name=VALUES(name), role=VALUES(role), is_active=VALUES(is_active);

-- ── 5. Pengaturan Sistem Awal ──
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
