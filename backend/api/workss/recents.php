<?php
/**
 * recents.php
 * REST API Endpoint: Daftar Naskah Ilmiah Terbit Terbaru
 * Method: GET
 */

require_once dirname(__DIR__, 2) . '/configs/apps.php';
require_once dirname(__DIR__, 2) . '/configs/databases.php';
require_once dirname(__DIR__) . '/helperss/api_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_helpers::responses_error('Metode permintaan tidak diizinkan. Gunakan GET.', 405);
}

$limits = (int)($_GET['limits'] ?? 10);
if ($limits < 1 || $limits > 50) {
    $limits = 10;
}

try {
    $db = databases::get_connections();
    $sql = "
        SELECT 
            w.id,
            w.title,
            w.abstract,
            w.year,
            w.supervisor,
            w.view_count,
            w.download_count,
            w.published_at,
            u.name AS author_name,
            u.nim_nip AS author_nim,
            sp.name AS prodi_name,
            f.name AS faculty_name,
            f.code AS faculty_code,
            wt.name AS work_type_name
        FROM works w
        JOIN users u ON w.author_id = u.id
        JOIN study_programs sp ON w.prodi_id = sp.id
        JOIN faculties f ON sp.faculty_id = f.id
        JOIN work_types wt ON w.work_type_id = wt.id
        WHERE w.status = 'published'
        ORDER BY w.published_at DESC, w.id DESC
        LIMIT :limits
    ";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':limits', $limits, PDO::PARAM_INT);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format output
    $formatted_items = array_map(function($item) {
        return [
            'id'             => (int)$item['id'],
            'title'          => $item['title'],
            'abstract'       => $item['abstract'],
            'year'           => (int)$item['year'],
            'supervisor'     => $item['supervisor'],
            'view_count'     => (int)$item['view_count'],
            'download_count' => (int)$item['download_count'],
            'published_at'   => $item['published_at'],
            'author'         => [
                'name'    => $item['author_name'],
                'nim_nip' => $item['author_nim']
            ],
            'study_program'  => $item['prodi_name'],
            'faculty'        => [
                'code' => $item['faculty_code'],
                'name' => $item['faculty_name']
            ],
            'work_type'      => $item['work_type_name']
        ];
    }, $items);

    api_helpers::responses_success($formatted_items, 'Daftar naskah terbaru berhasil dimuat.');

} catch (Exception $exceptions) {
    error_log("API Recents Error: " . $exceptions->getMessage());
    api_helpers::responses_error('Gagal memuat katalog karya terbaru.', 500);
}
