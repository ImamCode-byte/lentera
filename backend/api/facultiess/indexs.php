<?php
/**
 * indexs.php
 * REST API Endpoint: Daftar Fakultas & Statistik Koleksi
 * Method: GET
 */

require_once dirname(__DIR__, 2) . '/configs/apps.php';
require_once dirname(__DIR__, 2) . '/configs/databases.php';
require_once dirname(__DIR__) . '/helperss/api_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_helpers::responses_error('Metode permintaan tidak diizinkan. Gunakan GET.', 405);
}

try {
    $db = databases::get_connections();
    $sql = "
        SELECT 
            f.id,
            f.code,
            f.name,
            f.sort_order,
            COUNT(CASE WHEN w.status = 'published' THEN w.id END) AS works_count
        FROM faculties f
        LEFT JOIN study_programs sp ON sp.faculty_id = f.id
        LEFT JOIN works w ON w.prodi_id = sp.id
        WHERE f.is_active = 1
        GROUP BY f.id, f.code, f.name, f.sort_order
        ORDER BY f.sort_order ASC
    ";

    $stmt = $db->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formatted_rows = array_map(function($item) {
        return [
            'id'          => (int)$item['id'],
            'code'        => $item['code'],
            'name'        => $item['name'],
            'works_count' => (int)$item['works_count']
        ];
    }, $rows);

    api_helpers::responses_success($formatted_rows, 'Daftar fakultas berhasil dimuat.');

} catch (Exception $exceptions) {
    error_log("API Faculties Error: " . $exceptions->getMessage());
    api_helpers::responses_error('Gagal memuat daftar fakultas.', 500);
}
