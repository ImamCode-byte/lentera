-- ============================================================
-- 014_add_waiting_payment_enum.sql
-- Migrasi Non-Destruktif:
-- 1. Menambahkan nilai 'waiting_payment' pada kolom status di tabel works
-- 2. Menambahkan nilai 'in_review' dan 'acc_payment' pada kolom action di tabel reviews
-- ============================================================

-- 1. Perbarui ENUM kolom status pada tabel works
ALTER TABLE works 
MODIFY COLUMN status ENUM('draft','submitted','in_review','revision','waiting_payment','published','rejected') 
NOT NULL DEFAULT 'draft';

-- 2. Perbarui ENUM kolom action pada tabel reviews
ALTER TABLE reviews 
MODIFY COLUMN action ENUM('approve','revision','reject','in_review','acc_payment') 
NOT NULL;
