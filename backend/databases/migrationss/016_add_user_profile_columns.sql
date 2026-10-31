-- ============================================================
-- 016_add_user_profile_columns.sql
-- Migrasi Non-Destruktif:
-- Menambahkan kolom username, affiliation, country, phone, orcid_id,
-- dan is_reviewer_candidate pada tabel users
-- ============================================================

ALTER TABLE users 
ADD COLUMN IF NOT EXISTS username VARCHAR(60) NULL AFTER name,
ADD COLUMN IF NOT EXISTS affiliation VARCHAR(150) NULL DEFAULT 'Universitas Madura' AFTER prodi_id,
ADD COLUMN IF NOT EXISTS country VARCHAR(60) NULL DEFAULT 'Indonesia' AFTER affiliation,
ADD COLUMN IF NOT EXISTS phone VARCHAR(30) NULL AFTER country,
ADD COLUMN IF NOT EXISTS orcid_id VARCHAR(50) NULL AFTER phone,
ADD COLUMN IF NOT EXISTS is_reviewer_candidate TINYINT(1) DEFAULT 0 AFTER bio;

-- Tambahkan index unik untuk username jika belum ada
SET @dbname = DATABASE();
SET @tablename = "users";
SET @keyname = "uk_users_username";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE table_name = @tablename
      AND table_schema = @dbname
      AND index_name = @keyname
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD UNIQUE KEY ", @keyname, " (username)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

