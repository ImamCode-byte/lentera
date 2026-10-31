-- ============================================================
-- 017_add_payment_verification_columns.sql
-- Migrasi Non-Destruktif:
-- Menambahkan kolom admin_notes dan verified_by pada tabel paymentss
-- untuk pencatatan audit verifikasi pembayaran oleh admin
-- ============================================================

ALTER TABLE paymentss 
ADD COLUMN IF NOT EXISTS admin_notes TEXT NULL AFTER proof_file,
ADD COLUMN IF NOT EXISTS verified_by INT NULL AFTER admin_notes;

