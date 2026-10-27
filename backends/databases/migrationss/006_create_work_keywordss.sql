-- ============================================================
-- 006_create_work_keywords.sql
-- Kata Kunci per Karya Ilmiah
-- Relasi: work_id -> works.id (CASCADE on delete)
-- ============================================================

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
