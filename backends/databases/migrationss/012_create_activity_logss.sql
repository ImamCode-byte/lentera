-- ============================================================
-- 012_create_activity_logs.sql
-- Log Aktivitas Sistem (Audit Trail)
-- Relasi: user_id -> users.id (SET NULL — audit tetap tersimpan)
-- ============================================================

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
