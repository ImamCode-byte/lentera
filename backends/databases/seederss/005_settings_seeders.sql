-- ============================================================
-- 005_settings_seeder.sql
-- Konfigurasi Awal Sistem LENTERA
-- ============================================================

INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('site_name',          'LENTERA',                                 'general'),
('site_tagline',       'Menerangi Karya, Menginspirasi Generasi', 'general'),
('university_name',    'Universitas Madura',                      'general'),
('university_url',     'https://unira.ac.id',                     'general'),
('contact_email',      'lentera@unira.ac.id',                     'contact'),
('contact_phone',      '+62 812-3456-7890',                       'contact'),
('contact_address',    'Jl. Raya Panglegur No. 3, Pamekasan, Madura, Jawa Timur', 'contact'),
('max_file_size',      '20971520',                                'upload'), -- 20 MB
('allowed_formats',    'pdf',                                     'upload'),
('submission_rules',   'Format PDF maksimal 20MB. Pastikan naskah telah disetujui dosen pembimbing.', 'submission')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value), setting_group=VALUES(setting_group);
