-- ============================================================
-- 003_create_users.sql
-- Tabel Pengguna (Penulis, Kurator, Admin)
-- Relasi: prodi_id -> study_programs.id (SET NULL on delete, CASCADE on update)
-- ============================================================

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
