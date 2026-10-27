-- ============================================================
-- 003_work_types_seeder.sql
-- Data Awal Tipe Karya Ilmiah
-- ============================================================

INSERT INTO work_types (code, name, description) VALUES
('SKR', 'Skripsi',               'Tugas akhir jenjang sarjana (S1)'),
('TES', 'Tesis',                 'Tugas akhir jenjang magister (S2)'),
('JRN', 'Jurnal Ilmiah',         'Artikel ilmiah yang dipublikasikan di jurnal atau prosiding'),
('LPK', 'Laporan Kerja Praktik', 'Laporan kegiatan kerja praktik atau magang industri'),
('LPN', 'Laporan Penelitian',    'Laporan hasil penelitian mandiri atau hibah dosen dan mahasiswa')
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description);
