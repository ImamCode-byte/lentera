-- ============================================================
-- 004_admin_seeder.sql
-- Akun Default Super Admin LENTERA
-- Email    : admin@lentera.unira.ac.id
-- Password : LenteraAdmin2026! (dihash dengan PASSWORD_BCRYPT)
-- ============================================================

INSERT INTO users (name, email, password, role, is_active) VALUES
(
    'Super Admin LENTERA',
    'admin@lentera.unira.ac.id',
    -- Hash default untuk: LenteraAdmin2026! (atau jalankan php backend/database/seed.php untuk rehash otomatis)
    '$2y$12$4L64g0d2j5HkU7bK0P2XqO6uWvR8bF9wB3nQ4mE7yZ1cT5aV0gK2e',
    'admin',
    TRUE
)
ON DUPLICATE KEY UPDATE name=VALUES(name), role=VALUES(role), is_active=VALUES(is_active);
