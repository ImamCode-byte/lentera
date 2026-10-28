-- ============================================================
-- 008_create_reviews.sql
-- Catatan & Keputusan Review Kurator
-- Relasi:
--   work_id    -> works.id (CASCADE)
--   curator_id -> users.id (RESTRICT)
-- ============================================================

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
