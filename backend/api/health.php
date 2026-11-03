<?php
/**
 * health.php
 * REST API Health Check Endpoint untuk Platform Lentera
 * Digunakan untuk monitoring ketersediaan server, PHP, dan koneksi basis data
 * Method: GET
 */

require_once dirname(__DIR__, 1) . '/configs/apps.php';
require_once dirname(__DIR__, 1) . '/configs/databases.php';
require_once __DIR__ . '/helperss/api_helpers.php';

// Pastikan hanya metode GET yang diperkenankan
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    api_helpers::responses_error('Metode permintaan tidak diizinkan. Gunakan GET.', 405);
}

try {
    // 1. Uji koneksi ke basis data MySQL
    $db = databases::get_connections();
    $db_status = 'connected';
    
    // Hitung ringkasan naskah terbit
    $stmt = $db->query("SELECT COUNT(*) AS total_works FROM works WHERE status = 'published'");
    $total_works = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total_works'] ?? 0);
    
} catch (Exception $e) {
    $db_status = 'disconnected';
    $total_works = 0;
}

$payload = [
    'status'      => ($db_status === 'connected') ? 'healthy' : 'degraded',
    'application' => 'LENTERA (Laman Etalase Karya Tulis Ilmiah Terpadu Akademik)',
    'institution' => 'Universitas Madura (UNIRA)',
    'version'     => '1.0.0',
    'environment' => defined('APP_ENV') ? APP_ENV : 'development',
    'php_version' => PHP_VERSION,
    'database'    => [
        'status'         => $db_status,
        'engine'         => 'MySQL 8.0 InnoDB (PDO)',
        'database_name'  => defined('DB_NAME') ? DB_NAME : 'db_lentera',
        'published_works'=> $total_works
    ],
    'uptime_check'=> date('Y-m-d H:i:s'),
    'api_endpoints' => [
        'recent_works' => 'http://localhost/lentera/backend/api/workss/recents.php',
        'search_works' => 'http://localhost/lentera/backend/api/workss/searchs.php',
        'faculties'    => 'http://localhost/lentera/backend/api/facultiess/indexs.php',
        'auth_login'   => 'http://localhost/lentera/backend/api/auths/logins.php'
    ]
];

if ($db_status === 'connected') {
    api_helpers::responses_success($payload, 'Sistem Lentera dan basis data beroperasi normal (Healthy).', 200);
} else {
    api_helpers::responses_error('Layanan basis data tidak terhubung.', 503, $payload);
}

