-- ============================================================
-- 002_study_programs_seeder.sql
-- Data Awal Program Studi Universitas Madura
-- ============================================================

INSERT INTO study_programs (code, name, degree, faculty_id) VALUES
('FH-HKM',   'Hukum',                       'S1', (SELECT id FROM faculties WHERE code='FH')),
('FEB-MNJ',  'Manajemen',                   'S1', (SELECT id FROM faculties WHERE code='FEB')),
('FEB-AKT',  'Akuntansi',                   'S1', (SELECT id FROM faculties WHERE code='FEB')),
('FEB-BDG',  'Bisnis Digital',              'S1', (SELECT id FROM faculties WHERE code='FEB')),
('FIA-ADP',  'Administrasi Publik',         'S1', (SELECT id FROM faculties WHERE code='FIA')),
('FP-PTK',   'Peternakan',                  'S1', (SELECT id FROM faculties WHERE code='FP')),
('FP-AGB',   'Agribisnis',                  'S1', (SELECT id FROM faculties WHERE code='FP')),
('FT-TSP',   'Teknik Sipil',               'S1', (SELECT id FROM faculties WHERE code='FT')),
('FT-INF',   'Informatika',                'S1', (SELECT id FROM faculties WHERE code='FT')),
('FT-TIN',   'Teknik Industri',            'S1', (SELECT id FROM faculties WHERE code='FT')),
('FKIP-PBI', 'Pendidikan Bahasa Indonesia', 'S1', (SELECT id FROM faculties WHERE code='FKIP')),
('FKIP-PMT', 'Pendidikan Matematika',      'S1', (SELECT id FROM faculties WHERE code='FKIP')),
('FKIP-PBG', 'Pendidikan Bahasa Inggris',  'S1', (SELECT id FROM faculties WHERE code='FKIP')),
('PPS-HKM',  'Hukum',                      'S2', (SELECT id FROM faculties WHERE code='PPS')),
('PPS-MNJ',  'Manajemen',                  'S2', (SELECT id FROM faculties WHERE code='PPS'))
ON DUPLICATE KEY UPDATE name=VALUES(name), degree=VALUES(degree), faculty_id=VALUES(faculty_id);
