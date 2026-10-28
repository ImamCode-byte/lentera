<?php
/**
 * details.php
 * REST API Endpoint: Detail Lengkap Naskah Karya Ilmiah & Generator Sitasi
 * Method: GET
 */

require_once dirname(__DIR__, 2) . '/configs/apps.php';
require_once dirname(__DIR__, 2) . '/configs/databases.php';
require_once dirname(__DIR__) . '/helperss/api_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_helpers::responses_error('Metode permintaan tidak diizinkan. Gunakan GET.', 405);
}

$work_ids = (int)($_GET['ids'] ?? $_GET['id'] ?? 0);
if ($work_ids <= 0) {
    api_helpers::responses_error('Parameter ID karya (ids) tidak valid.', 422);
}

try {
    $db = databases::get_connections();

    // Ambil data naskah
    $sql = "
        SELECT 
            w.*,
            u.name AS author_name,
            u.nim_nip AS author_nim,
            u.email AS author_email,
            sp.name AS prodi_name,
            f.name AS faculty_name,
            f.code AS faculty_code,
            wt.name AS work_type_name
        FROM works w
        JOIN users u ON w.author_id = u.id
        JOIN study_programs sp ON w.prodi_id = sp.id
        JOIN faculties f ON sp.faculty_id = f.id
        JOIN work_types wt ON w.work_type_id = wt.id
        WHERE w.id = :ids
        LIMIT 1
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([':ids' => $work_ids]);
    $works = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$works) {
        api_helpers::responses_error('Karya ilmiah tidak ditemukan.', 404);
    }

    // Jika belum published, tolak akses publik
    if ($works['status'] !== 'published') {
        // Cek apakah ada token otentikasi pemilik / staf
        $auth_headers = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!empty($auth_headers)) {
            $user_payloads = api_helpers::authenticates_tokens();
            $is_owners = (int)$user_payloads['user_ids'] === (int)$works['author_id'];
            $is_staffs = in_array($user_payloads['user_roles'], ['admin', 'curator'], true);
            if (!$is_owners && !$is_staffs) {
                api_helpers::responses_error('Akses ditolak: Naskah ini belum dipublikasikan ke publik.', 403);
            }
        } else {
            api_helpers::responses_error('Akses ditolak: Naskah ini belum dipublikasikan ke publik.', 403);
        }
    } else {
        // Tambahkan counter view_count untuk karya published
        $db->prepare("UPDATE works SET view_count = view_count + 1 WHERE id = :ids")->execute([':ids' => $work_ids]);
        $works['view_count']++;
    }

    // Generator Sitasi
    $author_name = $works['author_name'];
    $year = $works['year'];
    $title = $works['title'];
    $type = $works['work_type_name'];
    $faculty = $works['faculty_name'];
    $prodi = $works['prodi_name'];
    $base_urls = get_configs('app_urls', 'http://localhost/lentera');
    $view_urls = "{$base_urls}/viewss/workss/details.php?ids={$works['id']}";

    $citations = [
        'apa'     => "{$author_name} ({$year}). {$title}. [{$type}, {$faculty}, Universitas Madura]. Repositori Lentera. {$view_urls}",
        'ieee'    => "{$author_name}, \"{$title},\" {$type}, {$prodi}, {$faculty}, Universitas Madura, Pamekasan, Indonesia, {$year}. [Online]. Tersedia: {$view_urls}",
        'harvard' => "{$author_name}, {$year}. {$title}. {$type}, {$faculty}, Universitas Madura. Tersedia di: <{$view_urls}> [Diakses " . date('d M Y') . "]."
    ];

    $response_datas = [
        'id'             => (int)$works['id'],
        'title'          => $works['title'],
        'abstract'       => $works['abstract'],
        'year'           => (int)$works['year'],
        'supervisor'     => $works['supervisor'],
        'status'         => $works['status'],
        'view_count'     => (int)$works['view_count'],
        'download_count' => (int)$works['download_count'],
        'published_at'   => $works['published_at'],
        'author'         => [
            'id'      => (int)$works['author_id'],
            'name'    => $works['author_name'],
            'nim_nip' => $works['author_nim'],
        ],
        'study_program'  => $works['prodi_name'],
        'faculty'        => [
            'code' => $works['faculty_code'],
            'name' => $works['faculty_name']
        ],
        'work_type'      => $works['work_type_name'],
        'has_file'       => !empty($works['file_path']),
        'download_url'   => !empty($works['file_path']) ? "{$base_urls}/frontend/web/viewss/workss/downloads.php?ids={$works['id']}" : null,
        'citations'      => $citations
    ];

    api_helpers::responses_success($response_datas, 'Detail karya ilmiah berhasil dimuat.');

} catch (Exception $exceptions) {
    error_log("API Details Error: " . $exceptions->getMessage());
    api_helpers::responses_error('Gagal memuat detail karya ilmiah.', 500);
}
