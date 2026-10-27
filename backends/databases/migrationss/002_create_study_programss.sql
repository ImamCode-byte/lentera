-- ============================================================
-- 002_create_study_programs.sql
-- Master Program Studi
-- Relasi: faculty_id -> faculties.id (RESTRICT on delete, CASCADE on update)
-- ============================================================

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
