-- ============================================================
-- 005_create_works.sql
-- Tabel Utama Karya Ilmiah
-- Relasi:
--   author_id    -> users.id (RESTRICT)
--   prodi_id     -> study_programs.id (RESTRICT)
--   work_type_id -> work_types.id (RESTRICT)
-- ============================================================

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
