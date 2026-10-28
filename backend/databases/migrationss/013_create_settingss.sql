-- ============================================================
-- 013_create_settings.sql
-- Konfigurasi & Pengaturan Sistem Dinamis
-- ============================================================

CREATE TABLE IF NOT EXISTS settings (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    setting_key   VARCHAR(50) NOT NULL,
    setting_value TEXT NULL,
    setting_group VARCHAR(50) DEFAULT 'general',
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_settings_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
