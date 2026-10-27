-- ============================================================
-- 007_create_work_files.sql
-- File Dokumen PDF per Karya (Multi-Versi Riwayat)
-- Relasi: work_id -> works.id (CASCADE on delete)
-- ============================================================

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
