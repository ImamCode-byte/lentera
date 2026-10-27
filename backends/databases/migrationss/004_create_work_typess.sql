-- ============================================================
-- 004_create_work_types.sql
-- Master Tipe Karya Ilmiah (Skripsi, Tesis, Jurnal, dll)
-- ============================================================

CREATE TABLE IF NOT EXISTS work_types (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    code        VARCHAR(10) NOT NULL,
    name        VARCHAR(50) NOT NULL,
    description TEXT NULL,
    is_active   BOOLEAN DEFAULT TRUE,

    UNIQUE KEY uk_types_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
