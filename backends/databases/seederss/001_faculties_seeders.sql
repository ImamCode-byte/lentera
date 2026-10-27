-- ============================================================
-- 001_faculties_seeder.sql
-- Data Awal Fakultas Universitas Madura
-- ============================================================

INSERT INTO faculties (code, name, sort_order) VALUES
('FH',   'Fakultas Hukum',                                1),
('FEB',  'Fakultas Ekonomi dan Bisnis',                    2),
('FIA',  'Fakultas Ilmu Administrasi',                     3),
('FP',   'Fakultas Pertanian',                             4),
('FT',   'Fakultas Teknik',                                5),
('FKIP', 'Fakultas Keguruan dan Ilmu Pendidikan',          6),
('PPS',  'Program Pascasarjana',                           7)
ON DUPLICATE KEY UPDATE name=VALUES(name), sort_order=VALUES(sort_order);
