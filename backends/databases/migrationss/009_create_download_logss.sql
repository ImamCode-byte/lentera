-- ============================================================
-- 009_create_download_logs.sql
-- Log Unduhan Karya Ilmiah (Statistik & Akreditasi)
-- Relasi:
--   work_id -> works.id (SET NULL — histori statistik tetap aman)
--   user_id -> users.id (SET NULL — unduhan oleh publik/anonim diizinkan)
-- ============================================================

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
