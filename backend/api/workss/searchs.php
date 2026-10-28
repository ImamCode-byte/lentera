<?php
/**
 * searchs.php
 * REST API Endpoint: Pencarian & Filter Katalog Karya Ilmiah
 * Method: GET
 */

require_once dirname(__DIR__, 2) . '/configs/apps.php';
require_once dirname(__DIR__, 2) . '/configs/databases.php';
require_once dirname(__DIR__) . '/helperss/api_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_helpers::responses_error('Metode permintaan tidak diizinkan. Gunakan GET.', 405);
}

$keywords = trim($_GET['q'] ?? $_GET['keywords'] ?? '');
$faculty_codes = trim($_GET['faculties'] ?? '');
$type_ids = (int)($_GET['types'] ?? 0);
$years = (int)($_GET['years'] ?? 0);

$pages = max(1, (int)($_GET['pages'] ?? 1));
$limits = min(30, max(1, (int)($_GET['limits'] ?? 10)));
$offsets = ($pages - 1) * $limits;

try {
    $db = databases::get_connections();

    $where_clauses = ["w.status = 'published'"];
    $bind_params = [];

    if (!empty($keywords)) {
        $where_clauses[] = "(w.title LIKE :kw1 OR w.abstract LIKE :kw2 OR u.name LIKE :kw3 OR w.supervisor LIKE :kw4)";
        $search_terms = "%{$keywords}%";
        $bind_params[':kw1'] = $search_terms;
        $bind_params[':kw2'] = $search_terms;
        $bind_params[':kw3'] = $search_terms;
        $bind_params[':kw4'] = $search_terms;
    }

    if (!empty($faculty_codes)) {
        $where_clauses[] = "f.code = :fac_code";
        $bind_params[':fac_code'] = strtoupper($faculty_codes);
    }

    if ($type_ids > 0) {
        $where_clauses[] = "w.work_type_id = :type_id";
        $bind_params[':type_id'] = $type_ids;
    }

    if ($years > 0) {
        $where_clauses[] = "w.year = :work_year";
        $bind_params[':work_year'] = $years;
    }

    $where_sql = implode(' AND ', $where_clauses);

    // Hitung total hasil
    $count_sql = "
        SELECT COUNT(*) AS total
        FROM works w
        JOIN users u ON w.author_id = u.id
        JOIN study_programs sp ON w.prodi_id = sp.id
        JOIN faculties f ON sp.faculty_id = f.id
        WHERE {$where_sql}
    ";
    $count_stmt = $db->prepare($count_sql);
    foreach ($bind_params as $param => $val) {
        $count_stmt->bindValue($param, $val);
    }
    $count_stmt->execute();
    $total_rows = (int)$count_stmt->fetch()['total'];

    // Ambil data hasil pencarian
    $data_sql = "
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
        WHERE {$where_sql}
        ORDER BY w.published_at DESC, w.id DESC
        LIMIT :limits OFFSET :offsets
    ";

    $data_stmt = $db->prepare($data_sql);
    foreach ($bind_params as $param => $val) {
        $data_stmt->bindValue($param, $val);
    }
    $data_stmt->bindValue(':limits', $limits, PDO::PARAM_INT);
    $data_stmt->bindValue(':offsets', $offsets, PDO::PARAM_INT);
    $data_stmt->execute();
    $rows = $data_stmt->fetchAll(PDO::FETCH_ASSOC);

    $formatted_rows = array_map(function($item) {
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
    }, $rows);

    api_helpers::responses_success([
        'total'        => $total_rows,
        'page'         => $pages,
        'limit'        => $limits,
        'total_pages'  => ceil($total_rows / $limits),
        'items'        => $formatted_rows
    ], "Ditemukan {$total_rows} naskah yang sesuai.");

} catch (Exception $exceptions) {
    error_log("API Search Error: " . $exceptions->getMessage());
    api_helpers::responses_error('Gagal menjalankan pencarian katalog.', 500);
}
