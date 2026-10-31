-- ============================================================
-- 015_create_paymentss.sql
-- Migrasi Pembuatan Tabel Transaksi Pembayaran Publikasi (Article Processing Charge)
-- ============================================================

CREATE TABLE IF NOT EXISTS paymentss (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    work_id             INT NOT NULL,
    user_id             INT NOT NULL,
    invoice_number      VARCHAR(50) NOT NULL,
    amount              DECIMAL(12,2) NOT NULL DEFAULT 150000.00,
    payment_method      VARCHAR(50) DEFAULT 'QRIS Dhes Mart / DANA',
    destination_account VARCHAR(100) DEFAULT '087849264452 an. Imam (Dhes Mart)',
    sender_name         VARCHAR(100) NULL,
    reference_number    VARCHAR(100) NULL,
    proof_file          VARCHAR(255) NULL,
    status              ENUM('pending','completed','rejected') NOT NULL DEFAULT 'pending',
    paid_at             TIMESTAMP NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uk_paymentss_invoice (invoice_number),
    INDEX idx_paymentss_work (work_id),
    INDEX idx_paymentss_user (user_id),

    CONSTRAINT fk_paymentss_work
        FOREIGN KEY (work_id) REFERENCES works(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_paymentss_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

