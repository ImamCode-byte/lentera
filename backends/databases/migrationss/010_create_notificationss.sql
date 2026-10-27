-- ============================================================
-- 010_create_notifications.sql
-- Notifikasi In-App Pengguna
-- Relasi: user_id -> users.id (CASCADE)
-- ============================================================

CREATE TABLE IF NOT EXISTS notifications (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    title      VARCHAR(100) NOT NULL,
    message    TEXT NOT NULL,
    link       VARCHAR(255) NULL,
    is_read    BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_notif_user (user_id, is_read),
    INDEX idx_notif_date (created_at),

    CONSTRAINT fk_notif_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
