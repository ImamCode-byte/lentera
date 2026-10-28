<?php
/**
 * workss.php
 * REST API Endpoint: Daftar Karya Ilmiah Penulis Terautentikasi (Protected Route)
 * Method: GET
 * Headers: Authorization: Bearer <token>
 */

require_once dirname(__DIR__, 2) . '/configs/apps.php';
require_once dirname(__DIR__, 2) . '/configs/databases.php';
require_once dirname(__DIR__) . '/helperss/api_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_helpers::responses_error('Metode permintaan tidak diizinkan. Gunakan GET.', 405);
}

// Verifikasi Bearer Token
$user_tokens = api_helpers::authenticates_tokens();
$author_ids = (int)$user_tokens['user_ids'];

try {
    $db = databases::get_connections();
    $sql = "
        SELECT 
            w.id,
            w.title,
            w.abstract,
            w.status,
            w.year,
            w.supervisor,
            w.view_count,
            w.download_count,
            w.submitted_at,
            w.published_at,
            w.created_at,
            w.updated_at,
            sp.name AS prodi_name,
            wt.name AS work_type_name
        FROM works w
        JOIN study_programs sp ON w.prodi_id = sp.id
        JOIN work_types wt ON w.work_type_id = wt.id
        WHERE w.author_id = :author_ids
        ORDER BY w.updated_at DESC, w.id DESC
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([':author_ids' => $author_ids]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formatted_items = array_map(function($item) {
        return [
            'id'             => (int)$item['id'],
            'title'          => $item['title'],
            'abstract'       => $item['abstract'],
            'status'         => $item['status'],
            'year'           => (int)$item['year'],
            'supervisor'     => $item['supervisor'],
            'view_count'     => (int)$item['view_count'],
            'download_count' => (int)$item['download_count'],
            'submitted_at'   => $item['submitted_at'],
            'published_at'   => $item['published_at'],
            'created_at'     => $item['created_at'],
            'updated_at'     => $item['updated_at'],
            'study_program'  => $item['prodi_name'],
            'work_type'      => $item['work_type_name']
        ];
    }, $items);

    api_helpers::responses_success([
        'author' => [
            'id'    => $author_ids,
            'name'  => $user_tokens['user_names'],
            'email' => $user_tokens['user_emails'],
            'role'  => $user_tokens['user_roles']
        ],
        'total'  => count($formatted_items),
        'works'  => $formatted_items
    ], 'Karya ilmiah penulis berhasil dimuat.');

} catch (Exception $exceptions) {
    error_log("API Author Works Error: " . $exceptions->getMessage());
    api_helpers::responses_error('Gagal memuat karya penulis.', 500);
}
