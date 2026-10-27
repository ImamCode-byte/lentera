<?php
/**
 * apps.php
 * konfigurasi utama aplikasi lenteras
 * seluruh variabel dan pengaturan menggunakan lowercase dan akhiran s
 */

// pengaturan zona waktus
date_default_timezone_set('Asia/Jakarta');

// array konfigurasis
$app_configs = [
    'app_names'        => 'lenteras',
    'app_taglines'     => 'menerangi karya, menginspirasi generasi',
    'university_names' => 'universitas madura',
    'app_urls'         => 'http://localhost/lentera',
    'base_paths'       => dirname(__DIR__, 2),
    'upload_paths'     => dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'uploads',
    'session_lifetimes'=> 7200, // 2 jam
];

// fungsi pembantu untuk mengambil konfigurasis
function get_configs($keys, $defaults = null) {
    global $app_configs;
    return $app_configs[$keys] ?? $defaults;
}
